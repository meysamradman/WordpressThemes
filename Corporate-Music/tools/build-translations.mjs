import {readFile,writeFile} from 'node:fs/promises';
{
 const root=new URL('../radmanct/languages/',import.meta.url),messages=JSON.parse(await readFile(new URL('fa_IR.json',root),'utf8'));
 messages['']='Content-Type: text/plain; charset=UTF-8\nLanguage: fa_IR\nPlural-Forms: nplurals=2; plural=(n > 1);\n';
 const keys=Object.keys(messages).sort(),count=keys.length,table=28,translations=table+count*8;let offset=translations+count*8;
 const originals=keys.map(k=>Buffer.from(k)),values=keys.map(k=>Buffer.from(messages[k]));
 const header=Buffer.alloc(offset);header.writeUInt32LE(0x950412de,0);header.writeUInt32LE(count,8);header.writeUInt32LE(table,12);header.writeUInt32LE(translations,16);
 const parts=[];for(const [list,index]of [[originals,table],[values,translations]])for(let i=0;i<count;i++){header.writeUInt32LE(list[i].length,index+i*8);header.writeUInt32LE(offset,index+i*8+4);parts.push(list[i],Buffer.from([0]));offset+=list[i].length+1;}
 await writeFile(new URL('fa_IR.mo',root),Buffer.concat([header,...parts]));
}
