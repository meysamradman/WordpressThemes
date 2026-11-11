import {PageTitleFields} from './page-title-fields.js';
const {createElement:h,createRoot,useState} = window.wp.element;
function Editor({host}) {
  const [value,setValue]=useState(JSON.parse(host.dataset.value));
  const labels=JSON.parse(host.dataset.labels);
  return h(PageTitleFields,{value,labels,change:next=>{setValue(next);document.getElementById('rdct-page-title-value').value=JSON.stringify(next);}});
}
const host=document.getElementById('rdct-page-title-editor');
if(host)createRoot(host).render(h(Editor,{host}));
