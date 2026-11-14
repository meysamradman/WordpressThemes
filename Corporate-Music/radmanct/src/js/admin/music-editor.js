for(const root of document.querySelectorAll('[data-rdc-audio]')){
 let frame;const input=root.querySelector('input'),label=root.querySelector('[data-rdc-audio-lbl]');
 root.querySelector('[data-rdc-audio-pick]').addEventListener('click',()=>{
  frame??=wp.media({title:'انتخاب فایل صوتی',library:{type:'audio'},multiple:false});
  frame.off('select');frame.on('select',()=>{const item=frame.state().get('selection').first().toJSON();input.value=item.id;label.textContent=item.title||item.filename;});frame.open();
 });
 root.querySelector('[data-rdc-audio-clear]').addEventListener('click',()=>{input.value='0';label.textContent='فایلی انتخاب نشده';});
}
