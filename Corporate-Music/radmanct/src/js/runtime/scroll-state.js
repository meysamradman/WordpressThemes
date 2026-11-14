const scrollers=new WeakMap();
export const getSmoothScroller=doc=>scrollers.get(doc)||null;
export function setSmoothScroller(doc,scroller){
 if(getSmoothScroller(doc)===scroller)return;
 if(scroller)scrollers.set(doc,scroller);else scrollers.delete(doc);
 doc.dispatchEvent(new doc.defaultView.CustomEvent('rdc:scr-mode'));
}
