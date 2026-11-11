import {readFile,writeFile,mkdir,readdir,stat} from 'node:fs/promises';
import {resolve,dirname,relative,extname} from 'node:path';
import {fileURLToPath} from 'node:url';
import {createHash} from 'node:crypto';
import {spawnSync} from 'node:child_process';

const root=resolve(dirname(fileURLToPath(import.meta.url)),'..');
const imageRoot=resolve(root,'radmanct-components/images/components');
const manifestPath=resolve(imageRoot,'previews.json');
const jobsPath=resolve(root,'artifacts/library-image-jobs.json');
const hash=value=>createHash('sha256').update(value).digest('hex');
const json=async(path,fallback)=>{try{return JSON.parse(await readFile(path,'utf8'));}catch(error){if(error.code==='ENOENT')return fallback;throw error;}};
const save=async(path,value)=>{const text=JSON.stringify(value,null,2)+'\n';let old;try{old=await readFile(path,'utf8');}catch{}if(old!==text){await mkdir(dirname(path),{recursive:true});await writeFile(path,text);}};
async function fingerprint(item){
  const files=[];
  for(const name of item.dependencies){const path=resolve(root,name);if(!path.startsWith(root+'/')&&!path.startsWith(root+'\\'))throw Error('Invalid preview dependency');let contents;try{contents=await readFile(path);}catch(error){if(error.code!=='ENOENT')throw error;contents=Buffer.from('missing');}files.push([name,hash(contents)]);}
  return hash(JSON.stringify({version:1,metadata:item.metadata,files}));
}
async function dependencies(entry){
  const names=new Set(['radmanct-components/inc/components/library.php','radmanct-components/inc/components/library-catalog.php','radmanct-components/inc/components/library-preview.php','radmanct-components/inc/components/presentation.php','radmanct-components/inc/components/image-media.php','radmanct-components/inc/components/image-loading.php','radmanct-components/inc/components/values.php']);
  const visit=async path=>{
    path=resolve(path);const name=relative(root,path).replaceAll('\\','/');if(name.startsWith('../')||names.has(name))return;names.add(name);
    let text;try{text=await readFile(path,'utf8');}catch{return;}
    for(const match of text.matchAll(/(?:import\s*(?:[^'"\n]*?from\s*)?|import\s*\(\s*|@import\s*)['"](\.[^'"]+)['"]/g))await visit(resolve(dirname(path),match[1]));
  };
  const component=entry.component;
  const dir=resolve(root,'radmanct-components/inc/components');
  for(const name of await readdir(dir)){if(name===component+'.php'||name.startsWith(component+'-')&&name.endsWith('.php'))names.add('radmanct-components/inc/components/'+name);}
  const walk=async path=>{try{for(const item of await readdir(path,{withFileTypes:true})){if(item.isDirectory())await walk(resolve(path,item.name));else names.add(relative(root,resolve(path,item.name)).replaceAll('\\','/'));}}catch(error){if(error.code!=='ENOENT')throw error;}};
  await walk(resolve(dir,component==='text-reveal'?'text':component));
  for(const name of await readdir(resolve(root,'radmanct-components/languages'))){if(name.endsWith('-samples.json'))names.add('radmanct-components/languages/'+name);}
  for(const key of Object.values(entry.entries))await visit(resolve(root,'radmanct-components',key));
  if(entry.script){await visit(resolve(root,'radmanct-components/src/js/admin/component-preview.js'));await visit(resolve(root,'radmanct-components/src/js/components',component==='text-reveal'?'text.js':component==='carousel'?'carousel-entry.js':component+'.js'));}
  for(const name of ['form','balance','horizon'])names.add('radmanct-components/images/components/samples/'+name+'.webp');
  return [...names].sort();
}
export async function refreshLibraryImages(){
  const manifest=await json(manifestPath,{version:1,items:{}});const status={};
  for(const [id,item] of Object.entries(manifest.items)){status[id]=await fingerprint(item);}
  await save(resolve(imageRoot,'preview-status.json'),status);
}

if(process.argv[1]&&resolve(process.argv[1])===fileURLToPath(import.meta.url)){
  const args=process.argv.slice(2);
  if(args[0]==='plan'){
    const url=new URL(args[1]||'http://127.0.0.1:8094/library-dev.php?view=jobs');
    if(!['127.0.0.1','localhost'].includes(url.hostname))throw Error('Use the isolated local preview endpoint.');
    const response=await fetch(url);if(!response.ok)throw Error('Preview catalog is unavailable.');const entries=await response.json();
    const jobs=[];
    for(const entry of entries){const item={id:entry.id,filename:entry.id+'.webp',metadata:entry,dependencies:await dependencies(entry)};item.fingerprint=await fingerprint(item);jobs.push(item);}
    await save(jobsPath,jobs);console.log(`Prepared ${jobs.length} image slots. No images generated.`);
  }else if(args[0]==='import'){
    if(!args[1])throw Error('Provide the directory containing approved component screenshots.');
    const source=resolve(args[1]);const jobs=await json(jobsPath,null);if(!jobs)throw Error('Run plan first.');
    const manifest=await json(manifestPath,{version:1,width:960,height:600,items:{}});
    let count=0;
    for(const item of jobs){
      if(!/^[a-z0-9-]+$/.test(item.id))throw Error('Invalid sample identifier');
      let input;for(const extension of ['.webp','.png','.jpg','.jpeg']){const candidate=resolve(source,item.id+extension);try{if((await stat(candidate)).isFile()){input=candidate;break;}}catch{}}
      if(!input)continue;
      const digest=await fingerprint(item);const file=item.id+'-'+digest.slice(0,12)+'.webp';
      const php=args[2]||'php';const result=spawnSync(php,['-d','extension=gd',resolve(root,'tools/library-image-convert.php'),input,resolve(imageRoot,file)],{encoding:'utf8'});
      if(result.status!==0)throw Error(result.stderr||result.stdout||'WebP conversion failed; supply the PHP binary with GD.');
      manifest.items[item.id]={file,fingerprint:digest,metadata:item.metadata,dependencies:item.dependencies,source:'user-provided screenshot'};count++;
    }
    await save(manifestPath,manifest);await refreshLibraryImages();console.log(`Imported ${count} approved screenshots. Source files preserved.`);
  }else if(args[0]==='refresh'){await refreshLibraryImages();}else throw Error('Use plan, import <directory> [php-binary], or refresh.');
}
