for(const root of document.querySelectorAll('[data-rdc-audio]')){
 let frame;const input=root.querySelector('input'),label=root.querySelector('[data-rdc-audio-lbl]');
 const update=(value,text)=>{input.value=String(value);label.textContent=text;input.dispatchEvent(new Event('input',{bubbles:true}));input.dispatchEvent(new Event('change',{bubbles:true}));};
 root.querySelector('[data-rdc-audio-pick]').addEventListener('click',()=>{
  frame??=wp.media({title:'انتخاب فایل صوتی',library:{type:'audio'},multiple:false});
  frame.off('select');frame.on('select',()=>{const item=frame.state().get('selection').first().toJSON();update(item.id,item.title||item.filename);});frame.open();
 });
 root.querySelector('[data-rdc-audio-clear]').addEventListener('click',()=>{update(0,'فایلی انتخاب نشده');});
}
