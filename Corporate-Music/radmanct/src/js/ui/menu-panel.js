import {initializePanelBranches} from './panel/branches.js';

export function initializeMenuPanel(dialog, trigger, close, desktop) {
 if (!trigger || !close || typeof dialog.showModal !== 'function') return () => {};
 const doc = dialog.ownerDocument;
 const view = doc.defaultView;
 const branches = initializePanelBranches(dialog);
 const lock = locked => doc.dispatchEvent(new view.CustomEvent('rdc:pnl-lock', {detail:{locked}}));
 const alignClose = () => {
  if (!dialog.open || desktop.matches) return;
  const box = trigger.getBoundingClientRect();
  const frame = dialog.getBoundingClientRect();
  const rtl = view.getComputedStyle(dialog).direction === 'rtl';
  dialog.style.setProperty('--rdc-pnl-cls-top', `${Math.max(0,box.top)}px`);
  dialog.style.setProperty('--rdc-pnl-cls-st', `${Math.max(0,rtl ? frame.right-box.right : box.left-frame.left)}px`);
 };
 const hide = () => { if (dialog.open) dialog.close(); };
 const resize = () => { if (desktop.matches) hide(); else alignClose(); };
 const show = () => {
  if (desktop.matches || dialog.open) return;
  lock(true);
  dialog.showModal();
  dialog.scrollTop = 0;
  alignClose();
  trigger.setAttribute('aria-expanded','true');
  close.focus({preventScroll:true});
 };
 const closed = () => {
  if (dialog.open) return;
  branches.reset();
  trigger.setAttribute('aria-expanded','false');
  lock(false);
  if (!desktop.matches) trigger.focus({preventScroll:true});
 };
 const cancel = event => { event.preventDefault(); if (!branches.back()) hide(); };
 const follow = event => {
  const link = event.target.closest('a[href]');
  if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
  hide();
 };
 trigger.addEventListener('click',show);
 close.addEventListener('click',hide);
 dialog.addEventListener('close',closed);
 dialog.addEventListener('cancel',cancel);
 dialog.addEventListener('click',follow);
 view.addEventListener('resize',resize);
 desktop.addEventListener('change',resize);
 return () => {
  trigger.removeEventListener('click',show);
  close.removeEventListener('click',hide);
  dialog.removeEventListener('close',closed);
  dialog.removeEventListener('cancel',cancel);
  dialog.removeEventListener('click',follow);
  view.removeEventListener('resize',resize);
  desktop.removeEventListener('change',resize);
  branches.destroy();
  if(dialog.open){dialog.close();lock(false);}
  trigger.setAttribute('aria-expanded','false');
 };
}
