import test from 'node:test';
import assert from 'node:assert/strict';
import {readFile,readdir} from 'node:fs/promises';

const theme=new URL('../radmanct/',import.meta.url);
async function sources(directory){
 const files=await readdir(directory,{withFileTypes:true});
 const groups=await Promise.all(files.filter(file=>!['dist','fonts','languages'].includes(file.name)).map(async file=>{
  const url=new URL(file.name+(file.isDirectory()?'/':''),directory);
  return file.isDirectory()?sources(url):/\.(php|js|css)$/.test(file.name)?[{url,text:await readFile(url,'utf8')}]:[];
 }));
 return groups.flat();
}
const files=await sources(theme),all=files.map(file=>file.text).join('\n');

test('custom frontend identifiers do not reintroduce the retired prefix or a runtime rename map',()=>{
 for(const {url,text} of files){
  const active=text.replaceAll('rdct:preloader-seen','');
  assert.doesNotMatch(active,/\brdct[-:]|\brdct[A-Z]/,url.pathname);
  assert.doesNotMatch(text,/css-class-map\.json/,url.pathname);
 }
});

test('renamed dataset properties match their declared DOM attributes',()=>{
 const names=new Set([...all.matchAll(/\bdataset\??\.\s*(rdc[A-Z]\w*)/g)].map(match=>match[1]));
 assert.ok(names.size>=4);
 for(const name of names){
  const attribute='data-'+name.replace(/[A-Z]/g,char=>'-'+char.toLowerCase());
  assert.ok(all.includes(attribute),`${name} requires ${attribute}`);
 }
});

test('class parts use the fixed vocabulary while semantic renderer variants remain intact',()=>{
 for(const {url,text} of files.filter(file=>file.url.pathname.endsWith('.css'))){
  const classes=[...text.matchAll(/\.(rdc-[\w-]+)/g)].map(match=>match[1].split('--')[0]);
  for(const name of classes)assert.doesNotMatch(name,/(?:-|__)(?:description|text|title|label|item|wrapper|inner|scroll|preview|content|gallery)(?:-|__|$)/,url.pathname+': '+name);
 }
 assert.ok(all.includes('rdc-btn--outline'));
 assert.ok(all.includes('rdc-blog-pv--compact'));
});
