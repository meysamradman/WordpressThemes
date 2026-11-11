export function navigateTabs(event, onChange, orientation = 'horizontal') {
  const list = event.currentTarget;
  const target = event.target.closest('[role="tab"]');
  if (!target || target.closest('[role="tablist"]') !== list) return;
  const tabs = [...list.querySelectorAll('[role="tab"]')].filter((tab) =>
    tab.closest('[role="tablist"]') === list && !tab.disabled && tab.getAttribute('aria-disabled') !== 'true');
  const index = tabs.indexOf(target);
  if (index < 0 || !tabs.length) return;
  const rtl = getComputedStyle(list).direction === 'rtl';
  const forward = orientation === 'vertical' ? 'ArrowDown' : rtl ? 'ArrowLeft' : 'ArrowRight';
  const backward = orientation === 'vertical' ? 'ArrowUp' : rtl ? 'ArrowRight' : 'ArrowLeft';
  let next;
  if (event.key === 'Home') next = 0;
  else if (event.key === 'End') next = tabs.length - 1;
  else if (event.key === forward) next = (index + 1) % tabs.length;
  else if (event.key === backward) next = (index - 1 + tabs.length) % tabs.length;
  if (next === undefined) return;
  event.preventDefault();
  tabs[next].focus();
  onChange(tabs[next].dataset.tabId);
}
