document.querySelectorAll('[data-rdc-md]').forEach(root=>{
 let frame;const value=root.querySelector('[data-media-value]'),label=root.querySelector('[data-media-label]');
 root.querySelector('[data-media-choose]').addEventListener('click',()=>{frame??=wp.media({library:{type:'video'},multiple:false});frame.off('select');frame.on('select',()=>{const item=frame.state().get('selection').first().toJSON();value.value=item.id;label.textContent=item.title||item.filename;});frame.open();});
 root.querySelector('[data-media-remove]').addEventListener('click',()=>{value.value='0';label.textContent='';});
});

document.querySelectorAll('[data-rdc-gal]').forEach(root=>{
 let frame,pending=false;
 const text=window.rdcGal,value=root.querySelector('[data-gallery-value]'),list=root.querySelector('[data-gallery-list]'),choose=root.querySelector('[data-gallery-choose]'),clear=root.querySelector('[data-gallery-remove]'),error=root.querySelector('[data-gallery-error]');
 const sync=(changed=false)=>{
  const items=[...list.children];value.value=items.map(item=>item.dataset.galleryId).join(',');
  root.querySelector('[data-gallery-count]').textContent=text.count.replace('%d',items.length);
  root.querySelector('[data-gallery-empty]').hidden=!!items.length;clear.disabled=!items.length;
  items.forEach((item,index)=>{item.querySelector('[data-gallery-move="-1"]').disabled=index===0;item.querySelector('[data-gallery-move="1"]').disabled=index===items.length-1;});
  if(changed)value.dispatchEvent(new Event('change',{bubbles:true}));
 };
 const fail=message=>{error.textContent=message;error.hidden=false;};
 const button=(label,action,icon)=>{
  const node=document.createElement('button');node.type='button';node.setAttribute('aria-label',label);
  if(icon){node.className='button button-small';node.dataset.galleryMove=action;const span=document.createElement('span');span.className=`dashicons dashicons-arrow-${icon}-alt2`;span.setAttribute('aria-hidden','true');node.append(span);}
  else{node.className='button-link-delete';node.dataset.galleryDelete='';node.textContent=label;}
  return node;
 };
 const render=images=>{
  const fragment=document.createDocumentFragment();
  for(const image of images){
   const item=document.createElement('li');item.className='rdc-adm-gal__itm';item.dataset.galleryId=image.id;
   const name=image.title||image.filename||String(image.id),link=document.createElement('a'),img=document.createElement('img');
   link.className='rdc-adm-gal__pv';link.href=image.url;link.target='_blank';link.rel='noopener';link.setAttribute('aria-label',text.view.replace('%s',name));
   const thumb=image.sizes?.thumbnail||image.sizes?.medium||image;
   img.src=thumb.url;img.width=thumb.width||150;img.height=thumb.height||150;img.alt='';img.loading='lazy';link.append(img);
   const caption=document.createElement('span');caption.className='rdc-adm-gal__name';caption.textContent=name;
   const actions=document.createElement('div');actions.className='rdc-adm-gal__acts';actions.append(button(text.earlier,'-1','up'),button(text.later,'1','down'),button(text.remove));
   item.append(link,caption,actions);fragment.append(item);
  }
  list.replaceChildren(fragment);sync(true);
 };
 choose.addEventListener('click',async()=>{
  if(pending)return;
  pending=true;choose.disabled=true;root.setAttribute('aria-busy','true');error.hidden=true;
  try{
   const ids=value.value.split(',').map(Number).filter(Boolean);
   let models=[];
   if(ids.length){
    // Fetch selected attachments in one native query, only when editing begins.
    const query=wp.media.query({include:ids,orderby:'post__in',type:'image',posts_per_page:100});
    await query.more();
    const byId=new Map(query.models.map(model=>[model.id,model]));
    if(ids.some(id=>!byId.has(id)))throw new Error('Missing gallery attachment');
    models=ids.map(id=>byId.get(id));
   }
   frame?.dispose();
   frame=wp.media({frame:'post',state:ids.length?'gallery-edit':'gallery',editing:true,multiple:true,selection:new wp.media.model.Selection(models,{multiple:true})});
   // Layout belongs to the theme; the modal manages images and their order.
   frame.state('gallery-edit').set('displaySettings',false);
   frame.on('update',library=>{
    const images=library.toJSON().filter(image=>image.type==='image');
    if(images.length>100){fail(text.limit);return;}
    render(images);
   });
   frame.open();
  }catch{fail(text.loadError);}
  finally{pending=false;choose.disabled=false;root.removeAttribute('aria-busy');}
 });
 list.addEventListener('click',event=>{
  const control=event.target.closest('button');if(!control)return;
  const item=control.closest('[data-gallery-id]');if(!item)return;
  if(control.hasAttribute('data-gallery-delete')){
   const next=item.nextElementSibling||item.previousElementSibling;item.remove();sync(true);(next?.querySelector('[data-gallery-delete]')||choose).focus();
  }else if(control.hasAttribute('data-gallery-move')){
   const earlier=control.dataset.galleryMove==='-1',neighbor=earlier?item.previousElementSibling:item.nextElementSibling;
   if(!neighbor)return;
   list.insertBefore(earlier?item:neighbor,earlier?neighbor:item);sync(true);
   if(control.disabled)item.querySelector('[data-gallery-move]:not(:disabled)')?.focus();else control.focus();
  }
 });
 clear.addEventListener('click',()=>{list.replaceChildren();sync(true);choose.focus();});
 sync();
});
