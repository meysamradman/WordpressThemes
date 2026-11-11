// Independent geometric prototype; no extracted Spline scene or runtime.
import * as T from 'three';
import {RoundedBoxGeometry} from 'three/addons/geometries/RoundedBoxGeometry.js';
import {GLTFExporter} from 'three/addons/exporters/GLTFExporter.js';
import {mergeVertices} from 'three/addons/utils/BufferGeometryUtils.js';
import {mkdir,writeFile} from 'node:fs/promises';
globalThis.FileReader=class {
 readAsArrayBuffer(blob){blob.arrayBuffer().then(value=>{this.result=value;this.onloadend?.();});}
};
const root=new T.Group();root.name='Robot_Root';
const metal=new T.MeshStandardMaterial({color:0x507e22,metalness:.92,roughness:.22});
const rim=new T.MeshStandardMaterial({color:0x9bce53,metalness:.8,roughness:.2});
const black=new T.MeshStandardMaterial({color:0x061509,metalness:.45,roughness:.19});
const eye=new T.MeshStandardMaterial({color:0xf2f5eb,metalness:.25,roughness:.22});
const dark=new T.MeshStandardMaterial({color:0x131c14,metalness:.8,roughness:.29});
function box(parent,name,w,h,d,r,mat,x=0,y=0,z=0){const m=new T.Mesh(new RoundedBoxGeometry(w,h,d,3,r),mat);m.name=name;m.position.set(x,y,z);parent.add(m);return m;}
function sphere(parent,name,r,mat,x,y,z){const m=new T.Mesh(new T.SphereGeometry(r,24,16),mat);m.name=name;m.position.set(x,y,z);parent.add(m);return m;}
const body=new T.Group();body.name='Robot_Body';root.add(body);
const torso=new T.Mesh(new T.CylinderGeometry(.3,.47,.72,32),metal);torso.position.y=-.64;body.add(torso);
box(body,'Body_Base',.83,.12,.6,.06,dark,0,-1.02);
const neck=new T.Mesh(new T.CylinderGeometry(.115,.14,.3,24),dark);neck.position.y=-.15;body.add(neck);
const base=new T.Mesh(new T.CylinderGeometry(.2,.3,.35,24),metal);base.position.y=-1.24;body.add(base);
const pivot=new T.Group();pivot.name='Head_Pivot';pivot.position.y=.01;root.add(pivot);
box(pivot,'Robot_Head',2.45,1.48,.65,.21,metal,0,.69);
box(pivot,'Head_Rim',2.29,1.33,.12,.18,rim,0,.7,.333);
box(pivot,'Face_Glass',2.17,1.22,.14,.16,black,0,.7,.39);
sphere(pivot,'Left_Eye',.185,eye,-.5,.84,.51);
sphere(pivot,'Right_Eye',.185,eye,.5,.84,.51);
const mouth=new T.Mesh(new T.TorusGeometry(.155,.013,8,40),rim);mouth.name='Mouth';mouth.position.set(0,.4,.48);pivot.add(mouth);
box(pivot,'Side_Left',.12,.52,.26,.05,dark,-1.24,.65);
box(pivot,'Side_Right',.12,.52,.26,.05,dark,1.24,.65);
root.traverse(node=>{if(node.geometry){const original=node.geometry;node.geometry=mergeVertices(original);original.dispose();}});
const target=new URL('../radman-library/modules/robot/models/',import.meta.url);await mkdir(target,{recursive:true});
const glb=await new GLTFExporter().parseAsync(root,{binary:true});
await writeFile(new URL('robot.glb',target),Buffer.from(glb));
console.log(`Independent robot GLB: ${Math.round(glb.byteLength/1024)} KiB`);
