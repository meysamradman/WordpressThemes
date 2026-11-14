for(const root of document.querySelectorAll('[data-rdl-media]')){
 const input=root.querySelector('input'),label=root.querySelector('[data-rdl-media-label]');let frame;
 const update=(value,text)=>{input.value=value;label.textContent=text;input.dispatchEvent(new Event('input',{bubbles:true}));input.dispatchEvent(new Event('change',{bubbles:true}));};
 root.querySelector('[data-rdl-media-pick]').addEventListener('click',()=>{const multiple=root.dataset.multiple==='1';frame??=wp.media({title:'انتخاب رسانه',library:{type:root.dataset.type},multiple});frame.off('select');frame.on('select',()=>{const items=frame.state().get('selection').toJSON();update(items.map(x=>x.id).join(','),items.map(x=>x.title||x.filename).join('، '));});frame.open();});
 root.querySelector('[data-rdl-media-clear]').addEventListener('click',()=>update('','فایلی انتخاب نشده'));
}
