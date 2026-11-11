import {containers, findContainer} from './builder-tree.js';
import {ResizeHandle} from './builder-resize.js';
import { ButtonControl } from '../../../../shared/admin/controls.js';
import { request } from './builder-api.js';
import { pickPreviewTarget, previewContentHeight } from './builder-selection.js';
import { findPreviewDrop, previewDropLine } from './builder-drag.js';
import { loadPreview } from './builder-preview-loader.js';
import { deviceAt } from './builder-devices.js';
import { prepareMenuPreview } from './builder-preview-menu.js';

const { createElement: h, useState, useEffect, useRef } = window.wp.element;
const { labels, direction: initialDirection } = window.rdctBuilder;

export function Preview({ layout, id, selected, onSelect, disabled, dragProps, dropResolver, over, actions = [], viewportWidth, direction = initialDirection, onResize, dropTarget, onLoadingChange }) {
  const open = true;
  const width = viewportWidth ?? 1440;
  const device = deviceAt(width);
  const mobile = device === 'mobile';
  const [html, setHtml] = useState('');
  const [status, setStatus] = useState('');
  const [failed, setFailed] = useState(false);
  const [attempt, setAttempt] = useState(0);
  const [renderedKey, setRenderedKey] = useState('');
  const frame = useRef(null);
  const viewport = useRef(null);
  const [availableWidth, setAvailableWidth] = useState(0);
  const scale = availableWidth > 0 ? Math.min(1, availableWidth / width) : 1;
  const renderedHtml = useRef('');
  const [height, setHeight] = useState(48);
  const [ready, setReady] = useState(false);
  const [targets, setTargets] = useState([]);
  const serialized = JSON.stringify(layout);
  const key = JSON.stringify([id, direction, serialized]);
  const interactive = renderedKey === key && ready && !status && !disabled;
  const loading = !failed && (renderedKey !== key || !ready || !!status);
  useEffect(() => { onLoadingChange?.(loading); }, [loading, onLoadingChange]);
  useEffect(() => () => onLoadingChange?.(false), [onLoadingChange]);
  useEffect(() => {
    const node = viewport.current;
    if (!node) return;
    const measure = () => {
      const style = getComputedStyle(node);
      setAvailableWidth(Math.max(1, node.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight)));
    };
    const observer = typeof ResizeObserver === 'function' ? new ResizeObserver(measure) : null;
    observer?.observe(node);
    window.addEventListener('resize', measure);
    measure();
    return () => { observer?.disconnect(); window.removeEventListener('resize', measure); };
  }, []);
  useEffect(() => {
    if (!open || !ready) return;
    const content = frame.current?.contentDocument?.querySelector('.rdct-preview');
    if (!content) return;
    let current = true;
    let pendingFrame = 0;
    const rowByItem = new Map(containers(layout).flatMap((row) => row.items.map((item) => [item.id, row.id])));
    const measure = () => {
      pendingFrame = 0;
      if (!current) return;
      setHeight(previewContentHeight(content));
      setTargets([...content.querySelectorAll('[data-builder-preview-row], [data-builder-preview-item]')].map((node) => {
        const itemId = node.getAttribute('data-builder-preview-item');
        const id = itemId || node.getAttribute('data-builder-preview-row');
        const bounds = node.getBoundingClientRect();
        return { id, item: !!itemId, rowId: itemId ? rowByItem.get(id) : id, rect: { left: bounds.left, top: bounds.top, width: bounds.width, height: bounds.height, right: bounds.right, bottom: bounds.bottom } };
      }).filter((target) => target.rect.width && target.rect.height));
    };
    const queue = () => { if (current && !pendingFrame) pendingFrame = requestAnimationFrame(measure); };
    const observer = typeof ResizeObserver === 'function' ? new ResizeObserver(queue) : null;
    observer?.observe(content);
    queue();
    frame.current.contentWindow.addEventListener('resize', queue);
    const view = frame.current.contentWindow;
    const fontReady = frame.current.contentDocument.fonts?.ready;
    fontReady?.then(queue).catch(() => {});
    return () => { current = false; observer?.disconnect(); cancelAnimationFrame(pendingFrame); view.removeEventListener('resize', queue); };
  }, [open, ready, html, width, serialized]);
  const highlight = () => {
    const doc = frame.current?.contentDocument;
    if (!doc) return;
    const accent = getComputedStyle(frame.current).getPropertyValue('--rdct-admin-accent').trim() || 'Highlight';
    doc.querySelectorAll('[data-builder-preview-item], [data-builder-preview-row]').forEach((node) => {
      const active = selected?.itemId ? node.getAttribute('data-builder-preview-item') === selected.itemId : node.getAttribute('data-builder-preview-row') === selected?.rowId;
      node.style.outline = active ? `2px solid ${accent}` : '';
      node.style.outlineOffset = active ? '-2px' : '';
    });
  };
  useEffect(highlight, [selected, html]);
  useEffect(() => loadPreview({
    request, id, layout: JSON.parse(serialized), direction, errorMessage: labels.requestError,
    delay: renderedHtml.current ? 400 : 0,
    onStart: () => { setStatus(labels.previewLoading); setFailed(false); },
    onSuccess: (value) => {
      if (renderedHtml.current !== value) {
        renderedHtml.current = value; setReady(false); setHtml(value);
      }
      setRenderedKey(key); setStatus('');
    },
    onError: (message) => { setStatus(message); setFailed(true); },
  }), [serialized, direction, id, attempt]);
  const resolveDrop = (x, y) => {
    const rect = frame.current?.getBoundingClientRect();
    if (!rect || !interactive || x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) return null;
    return findPreviewDrop(targets, (x - rect.left) / scale, (y - rect.top) / scale, layout, mobile, direction === 'rtl', device) || (!layout.rows.length ? { rowId: null, index: 0, token: 'empty' } : null);
  };
  dropResolver.current = resolveDrop;
  const insertion = previewDropLine(dropTarget,targets,layout,mobile,direction === 'rtl',device);
  useEffect(() => () => { dropResolver.current = null; }, [dropResolver]);
  return h('section', { className: 'rdct-bld-preview', 'aria-busy': loading },
    open && h('div', null,
      failed && h('p', {role:'alert'}, status),
      failed && h(ButtonControl, { disabled, onClick: () => setAttempt((value) => value + 1) }, labels.retryPreview),
      h('div', { className: 'rdct-bld-preview__viewport', ref: viewport }, html && h('div', { className: 'rdct-bld-preview__stage', style: { width: `${width * scale}px`, height: `${height * scale}px` } }, h('div', { className: 'rdct-bld-preview__frame', style: { width: `${width}px`, transform: `scale(${scale})` },
      },
        h('iframe', {
          ref: frame, title: labels.preview, srcDoc: html, sandbox: 'allow-same-origin', 'data-vue-devtools-ignore': '', referrerPolicy: 'no-referrer', tabIndex: -1,
          onError: () => { setStatus(labels.requestError); setFailed(true); },
          style: { height: `${height}px` },
          onLoad: () => {
            const doc = frame.current?.contentDocument;
            if (!doc) return;
            prepareMenuPreview(doc);
            setHeight(previewContentHeight(doc.querySelector('.rdct-preview')));
            setReady(true); highlight();
          },
        }),
        h('div', { className: 'rdct-bld-preview__selection', onClick: (event) => {
          if (!interactive) return;
          const doc = frame.current?.contentDocument;
          const rect = frame.current?.getBoundingClientRect();
          if (!doc || !rect) return;
          const target = pickPreviewTarget(doc, (event.clientX - rect.left) / scale, (event.clientY - rect.top) / scale);
          if (target) onSelect(target);
        } }, ...(interactive ? targets : []).map((target) => {
          const active = target.item ? selected?.itemId === target.id : !selected?.itemId && selected?.rowId === target.id;
          const row = findContainer(layout, target.rowId);
          const item = row?.items.find((entry) => entry.id === target.id);
          if (!row || (target.item && !item) || (!target.item && row.type === 'container')) return null;
          const title = target.item ? labels[item?.type] : row.type === 'container' ? labels.container : `${labels.row} ${layout.rows.indexOf(row) + 1}`;
          return h('div', { key: `${target.item}-${target.id}`, className: `rdct-bld-preview__box${target.item ? ' is-item' : ' is-row'}${item?.type === 'container' ? ' is-container' : ''}${active ? ' is-active' : ''}${over === target.id ? ' is-drop' : ''}`, style: { left: target.rect.left, top: target.rect.top, width: target.rect.width, height: target.rect.height } },
            h('button', { type: 'button', className: 'rdct-bld-preview__target', 'aria-label': title, 'aria-pressed': active, disabled: disabled || !ready || !!status,
              ...dragProps({rowId:target.rowId,...(target.item ? {itemId:target.id} : {})}),
              onClick: (event) => { event.stopPropagation(); onSelect({ id: target.id, item: target.item }); },
            }),
            h('div', { className: 'rdct-bld-preview__tools', onClick: (event) => event.stopPropagation() }, h(ButtonControl, { variant: 'ghost', title, 'aria-label': title, disabled, ...dragProps(!target.item && row.type === 'container' ? {rowId:row.parentId,itemId:row.id} : { rowId: target.rowId, ...(target.item ? { itemId: target.id } : {}) }), onClick: () => onSelect({ id: target.id, item: target.item }) }, title),
              active && actions),
            active && target.item && onResize && row.appearance?.rowLayout !== 'grid' && h(ResizeHandle, {target, parent:targets.find((entry)=>!entry.item && entry.id === row.id), item, device, rtl:direction === 'rtl', label:labels.resizeWidth, scale, onCommit:(appearance)=>onResize(row.id,item.id,appearance)}));
        }),insertion && h('div',{className:`rdct-bld-drop-line${insertion.inside ? ' is-inside' : ''}`,style:{left:insertion.left,top:insertion.top,width:insertion.width,height:insertion.height},'aria-hidden':true})))))));
}
