import * as T from 'three';
import {GLTFLoader} from 'three/addons/loaders/GLTFLoader.js';
import {RoomEnvironment} from 'three/addons/environments/RoomEnvironment.js';

function disposeModel(model){const geometries=new Set(),materials=new Set(),textures=new Set();model?.traverse(node=>{if(node.geometry)geometries.add(node.geometry);for(const mat of Array.isArray(node.material)?node.material:node.material?[node.material]:[]){materials.add(mat);for(const value of Object.values(mat))if(value?.isTexture)textures.add(value);}});textures.forEach(t=>t.dispose());materials.forEach(m=>m.dispose());geometries.forEach(g=>g.dispose());}

export async function mountRobot(root,cfg,isAlive){
 const host=root.querySelector('.rdl-bot__md'),canvas=root.querySelector('canvas');
 let renderer,model,environment,pmrem,room,raf=0,dead=false,visible=false,rect,last=0,returning=true;
 const events=new AbortController(),scene=new T.Scene(),target=new T.Quaternion(),initial=new T.Quaternion(),offset=new T.Quaternion(),euler=new T.Euler(0,0,0,'YXZ');
 let pivot,resizeObserver,visibilityObserver;
 const stop=()=>{cancelAnimationFrame(raf);raf=0;last=0;};
 const destroy=()=>{if(dead)return;dead=true;stop();events.abort();resizeObserver?.disconnect();visibilityObserver?.disconnect();disposeModel(model);environment?.dispose();room?.dispose();pmrem?.dispose();renderer?.dispose();renderer?.forceContextLoss();root.removeAttribute('data-rdl-bot-rdy');};
 try{
  renderer=new T.WebGLRenderer({canvas,alpha:cfg.transparent!==false,antialias:true,powerPreference:'low-power'});
  renderer.setPixelRatio(Math.min(devicePixelRatio||1,Math.max(1,Math.min(2,Number(cfg.dpr)||1.5))));
  renderer.setClearColor(0x111710,cfg.transparent===false?1:0);renderer.toneMapping=T.ACESFilmicToneMapping;renderer.toneMappingExposure=1.05;
  const camera=new T.PerspectiveCamera(34,1,.1,50);camera.position.fromArray(cfg.camera||[0,.35,6.5]);camera.lookAt(0,.12,0);
  room=new RoomEnvironment();pmrem=new T.PMREMGenerator(renderer);environment=pmrem.fromScene(room,.04);scene.environment=environment.texture;
  room.dispose();room=undefined;pmrem.dispose();pmrem=undefined;
  scene.add(new T.HemisphereLight(0xe6f4dc,0x24301e,2));
  const key=new T.DirectionalLight(0xfff5dc,3.5);key.position.set(-3,4,5);scene.add(key);
  const fill=new T.DirectionalLight(0xc5edaf,1.6);fill.position.set(3,1,-2);scene.add(fill);
  model=(await new GLTFLoader().loadAsync(root.dataset.rdlBotModel)).scene;
  if(dead||!isAlive()){if(dead)disposeModel(model);else destroy();return()=>{};}
  model.scale.setScalar(Number(cfg.scale)||1);model.rotation.fromArray([...(cfg.rotation||[0,0,0]),'XYZ']);scene.add(model);
  pivot=model.getObjectByName(cfg.pivot||'Head_Pivot');if(!pivot)throw new Error('Robot head pivot missing.');
  initial.copy(pivot.quaternion);target.copy(initial);
  const draw=(now)=>{
   raf=0;if(dead||!visible||document.hidden)return;
   const dt=last?Math.min((now-last)/1000,.05):1/60;last=now;
   const speed=Math.max(.1,Number(returning?cfg.return:cfg.follow)||8);
   pivot.quaternion.slerp(target,1-Math.exp(-speed*dt));
   renderer.render(scene,camera);
   if(pivot.quaternion.angleTo(target)>.0002)raf=requestAnimationFrame(draw);else last=0;
  };
  const request=()=>{if(!dead&&visible&&!document.hidden&&!raf)raf=requestAnimationFrame(draw);};
  const resize=()=>{rect=host.getBoundingClientRect();if(!rect.width||!rect.height)return;renderer.setSize(rect.width,rect.height,false);camera.aspect=rect.width/rect.height;camera.updateProjectionMatrix();request();};
  const reset=()=>{returning=true;target.copy(initial);request();};
  const move=event=>{
   if(event.pointerType==='touch'||!visible||!rect||cfg.interaction===false)return;
   const x=T.MathUtils.clamp((event.clientX-(rect.left+rect.width/2))/(innerWidth/2),-1,1),y=T.MathUtils.clamp((event.clientY-(rect.top+rect.height/2))/(innerHeight/2),-1,1);
   euler.set(y*T.MathUtils.degToRad(T.MathUtils.clamp(Number(cfg.pitch)||12,0,45)),x*T.MathUtils.degToRad(T.MathUtils.clamp(Number(cfg.yaw)||20,0,60)),0);
   target.copy(initial).multiply(offset.setFromEuler(euler));returning=false;request();
  };
  const opts={passive:true,signal:events.signal};
  document.addEventListener('pointermove',move,opts);
  document.addEventListener('pointerout',event=>{if(!event.relatedTarget)reset();},opts);
  window.addEventListener('blur',reset,opts);
  window.addEventListener('scroll',()=>{rect=host.getBoundingClientRect();},opts);
  document.addEventListener('visibilitychange',()=>{if(document.hidden)stop();else{reset();request();}},opts);
  canvas.addEventListener('webglcontextlost',event=>{event.preventDefault();destroy();root.dataset.rdlBotErr='1';},{signal:events.signal});
  window.addEventListener('pagehide',stop,opts);window.addEventListener('pageshow',request,opts);
  resizeObserver=new ResizeObserver(resize);resizeObserver.observe(host);
  visibilityObserver=new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;if(visible){resize();request();}else stop();});visibilityObserver.observe(host);
  resize();root.dataset.rdlBotRdy='1';return destroy;
 }catch(error){destroy();throw error;}
}
