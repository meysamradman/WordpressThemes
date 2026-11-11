import Pickr from '@simonwep/pickr';
import '@simonwep/pickr/dist/themes/monolith.min.css';

export function createColorPicker(button, {value, colors, label, resetLabel, onChange, onOpen, onClose}) {
  let timer; let draft; let active = true; let ready=false; let wantsShow=false; let current=value;
  const commit = () => { clearTimeout(timer); if (active && draft) { const next=draft; draft=null; onChange(next); } };
  const picker = Pickr.create({el:button,useAsButton:true,theme:'monolith',appClass:'rdct-color-picker',container:button.ownerDocument.body,
    position:'bottom-middle',padding:8,default:value,defaultRepresentation:'HEX',lockOpacity:true,comparison:false,closeOnScroll:true,
    swatches:colors,components:{preview:true,hue:true,opacity:false,interaction:{input:true,hex:true,rgba:false,hsla:false,hsva:false,cmyk:false,clear:false,save:false}},
    i18n:{'ui:dialog':label,'btn:toggle':label,'btn:swatch':label,'btn:last-color':resetLabel,'aria:input':`${label} HEX`,'aria:palette':label,'aria:hue':label},
  });
  const app=picker.getRoot().app; app.dir='ltr';
  picker.on('init',()=>{if(!active)return;ready=true;picker.setColor(current,true);if(wantsShow)picker.show();});
  picker.on('change',color=>{draft=color.toHEXA().toString().toLowerCase();clearTimeout(timer);timer=setTimeout(commit,120);});
  picker.on('changestop',commit).on('swatchselect',commit).on('show',onOpen).on('hide',()=>{commit();onClose();});
  return {
    show:()=>{wantsShow=true;if(ready)picker.show();},
    setColor(next) {clearTimeout(timer);draft=null;current=next;if (ready && picker.getColor()?.toHEXA().toString().toLowerCase() !== next.toLowerCase()) picker.setColor(next,true); },
    destroy() { if (!active) return;active=false;clearTimeout(timer);picker.destroy();app.remove(); },
  };
}
