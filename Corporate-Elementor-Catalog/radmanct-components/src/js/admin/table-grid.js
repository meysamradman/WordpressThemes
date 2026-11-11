import { normalizeTable, changeColumn, limits } from './table-data.js';
const { createElement:h,useState } = window.wp.element;

export function TableGrid({value,onChange,disabled=false,labels,ui}) {
  const data = normalizeTable(value);
  const [error,setError] = useState(false);
  const emit = (next) => {
    const normalized = normalizeTable(next);
    if (new TextEncoder().encode(JSON.stringify(normalized)).byteLength > 131072) { setError(true); return; }
    setError(false); onChange(normalized);
  };
  const button = (text,label,action,inactive=false) => h(ui?.Button || 'button',{type:'button','aria-label':label,title:label,disabled:disabled || inactive,onClick:action,...(ui ? {size:'small',variant:'outlined'} : {className:'e-btn e-btn-txt-border'})},text);
  const input = (value,label,maxLength,multiline,onChange) => ui
    ? h(ui.TextField,{size:'small',fullWidth:true,value,disabled,multiline,minRows:multiline ? 3 : undefined,inputProps:{maxLength,'aria-label':label},onChange})
    : h(multiline ? 'textarea' : 'input',{className:'elementor-input',type:multiline ? undefined : 'text',value,disabled,rows:multiline ? 3 : undefined,maxLength,'aria-label':label,onChange});
  const moveRow = (index,step) => { const rows = [...data.rows]; [rows[index],rows[index+step]] = [rows[index+step],rows[index]]; emit({...data,rows}); };
  const grid = h('fieldset',{className:`rdct-tbl-editor rdct-tbl-editor--${ui ? 'atomic' : 'classic'}`,disabled},
    h('legend',null,labels.table),
    error && h('p',{role:'alert'},labels.limit),
    h('div',{className:'rdct-tbl-editor__scroll',tabIndex:0,'aria-label':labels.table},h('table',null,
      h('thead',null,h('tr',null,h('th',null,labels.row),...data.columns.map((column,index) => h('th',{key:index},
        input(column,`${labels.column} ${index+1}`,500,false,(event) => emit({...data,columns:data.columns.map((cell,i) => i === index ? event.target.value : cell)})),
        h('div',{className:'rdct-tbl-editor__actions'},
          button('‹',labels.earlier,() => emit(changeColumn(data,index,'earlier')),index === 0),
          button('›',labels.later,() => emit(changeColumn(data,index,'later')),index === data.columns.length-1),
          button('×',labels.removeColumn,() => emit(changeColumn(data,index,'remove')),data.columns.length === 1)
        )
      )))),
      h('tbody',null,...data.rows.map((row,index) => h('tr',{key:index},
        h('th',{scope:'row'},index+1,h('div',{className:'rdct-tbl-editor__actions'},
          button('↑',labels.up,() => moveRow(index,-1),index === 0),button('↓',labels.down,() => moveRow(index,1),index === data.rows.length-1),
          button('+',labels.duplicate,() => emit({...data,rows:[...data.rows.slice(0,index+1),{cells:[...row.cells]},...data.rows.slice(index+1)]}),data.rows.length >= limits.rows),
          button('×',labels.removeRow,() => emit({...data,rows:data.rows.filter((_,i) => i !== index)}))
        )),
        ...row.cells.map((cell,column) => h('td',{key:column},input(cell,`${labels.row} ${index+1}, ${labels.column} ${column+1}`,3000,true,(event) => emit({...data,rows:data.rows.map((entry,i) => i === index ? {cells:entry.cells.map((text,j) => j === column ? event.target.value : text)} : entry)}))))
      )))
    )),
    h('div',{className:'rdct-tbl-editor__actions'},
      button(labels.addColumn,labels.addColumn,() => emit(changeColumn(data,0,'add')),data.columns.length >= limits.columns),
      button(labels.addRow,labels.addRow,() => emit({...data,rows:[...data.rows,{cells:data.columns.map(() => '')}]}),data.rows.length >= limits.rows)
    )
  );
  return ui ? h(ui.Box,{sx:(theme) => ({'--rdct-table-border':theme.palette.divider})},grid) : grid;
}
