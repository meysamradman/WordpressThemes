for(const root of document.querySelectorAll('[data-rdl-prd]')){
 const input=root.querySelector('[data-rdl-prd-gal]'),preview=root.querySelector('[data-rdl-prd-pv]'),msg=root.querySelector('[data-rdl-prd-msg]'),spec=root.querySelector('[data-rdl-prd-spec]');let frame;
 const show=items=>{input.value=items.map(item=>item.id).join(',');preview.replaceChildren();for(const item of items){const img=document.createElement('img');img.src=item.sizes?.thumbnail?.url||item.url;img.alt=item.alt||'';img.width=100;img.height=100;preview.append(img);}};
 root.querySelector('[data-rdl-prd-pick]').addEventListener('click',()=>{
  frame=wp.media({title:'گالری محصول',button:{text:'استفاده از تصاویر'},library:{type:'image'},multiple:true,frame:'post',state:'gallery-edit',editing:true});
  const selection=frame.state().get('library');for(const id of input.value.split(',').map(Number).filter(Boolean)){const attachment=wp.media.attachment(id);attachment.fetch();selection.add(attachment);}
  frame.on('update',items=>{if(items.length>20){msg.textContent='حداکثر ۲۰ تصویر مجاز است؛ انتخاب قبلی حفظ شد.';return;}show(items.toJSON());msg.textContent='پس از ذخیرهٔ محصول اعمال می‌شود.';});frame.open();
 });
 root.querySelector('[data-rdl-prd-clear]').addEventListener('click',()=>show([]));
 let next=spec.children.length;
 root.querySelector('[data-rdl-prd-add]').addEventListener('click',()=>{
  if(spec.children.length>=40){msg.textContent='حداکثر ۴۰ مشخصه مجاز است.';return;}
  const index=next++,row=document.createElement('p');row.dataset.rdlPrdRow='';
  const id=document.createElement('input');id.type='hidden';id.name=`rdl_prd[specifications][${index}][id]`;id.value='s'+Date.now().toString(36)+index;row.append(id);
  for(const [key,text] of [['label','عنوان'],['value','مقدار'],['unit','واحد']]){const label=document.createElement('label'),field=document.createElement('input');label.textContent=text+' ';field.name=`rdl_prd[specifications][${index}][${key}]`;label.append(field);row.append(label,document.createTextNode(' '));}
  const button=document.createElement('button');button.type='button';button.className='button-link-delete';button.dataset.rdlPrdDel='';button.textContent='حذف ردیف';row.append(button);spec.append(row);row.querySelector('input:not([type=hidden])').focus();
 });
 spec.addEventListener('click',event=>{if(event.target.closest('[data-rdl-prd-del]'))event.target.closest('[data-rdl-prd-row]').remove();});
}
