import {mountMenuHover} from './hover.js';
import {lockPanelScroll} from './scroll-lock.js';

export function fitSubmenu(submenu) {
  submenu.style.removeProperty('--rdct-menu-shift');
  submenu.style.removeProperty('--rdct-menu-height');
  submenu.style.removeProperty('--rdct-menu-block-shift');
  submenu.classList.remove('rdct-submenu--scroll');
  const style = getComputedStyle(submenu);
  if (style.position !== 'absolute') return;
  const bounds = submenu.getBoundingClientRect();
  const width = document.documentElement.clientWidth;
  const shift = Math.max(8 - bounds.left, Math.min(0, width - 8 - bounds.right));
  if (shift) submenu.style.setProperty('--rdct-menu-shift', `${style.direction === 'rtl' ? -shift : shift}px`);
  const layoutHeight = submenu.offsetHeight;
  const naturalHeight = Number.isFinite(layoutHeight) && layoutHeight > 0 ? layoutHeight : bounds.height;
  const height = document.documentElement.clientHeight;
  const blockShift = Math.max(8 - bounds.top, Math.min(0, height - 8 - (bounds.top + naturalHeight)));
  if (Number.isFinite(blockShift) && blockShift) submenu.style.setProperty('--rdct-menu-block-shift', `${blockShift}px`);
  const available = height - Math.max(8, bounds.top + blockShift) - 8;
  if (Number.isFinite(available) && available > 0 && naturalHeight > available) {
    submenu.style.setProperty('--rdct-menu-height', `${available}px`);
    submenu.classList.add('rdct-submenu--scroll');
  }
}

export function initializeMenus(scope = document) {
  const menus = [];
  const listeners = [];
  let disposed = false;
  const listen = (target, event, callback, options) => {
    target.addEventListener(event, callback, options);
    listeners.push(() => target.removeEventListener(event, callback, options));
  };
  const desktop = matchMedia('(min-width: 48rem)');
  const laptop = matchMedia('(min-width: 64rem)');
  const wide = matchMedia('(min-width: 90rem)');
  const device = () => wide.matches ? 'desktop' : laptop.matches ? 'laptop' : desktop.matches ? 'tablet' : 'mobile';
  const roots = scope.matches?.('[data-rdct-menu]') ? [scope] : scope.querySelectorAll('[data-rdct-menu]');
  roots.forEach((container) => {
    const button = container.querySelector(':scope > .rdct-menu-toggle');
    const navigation = container.querySelector(':scope > [data-rdct-panel-body]') || container.querySelector(':scope > .rdct-nav');
    if (!button || !navigation || container.dataset.enhanced) return;
    const originalHidden = button.hidden;
    const expandedMobile = container.hasAttribute('data-mobile-expanded');
    const drawerDevices = (container.dataset.drawerDevices ?? 'mobile').split(' ');
    const wantsDrawer = () => container.hasAttribute('data-rdct-drawer') && drawerDevices.includes(device());
    const hover = container.dataset.submenuTrigger === 'hover' ? matchMedia('(hover: hover) and (pointer: fine)') : null;
    const syncButton = () => { button.hidden = !desktop.matches && expandedMobile && !wantsDrawer(); };

    const submenus = [];
    (container.hasAttribute('data-panel-only') ? [] : navigation.querySelectorAll('.menu-item-has-children')).forEach((item, index) => {
      const submenu = Array.from(item.children).find((child) => child.classList.contains('sub-menu'));
      const link = Array.from(item.children).find((child) => child.tagName === 'A');
      if (!submenu || !link || !container.dataset.submenuLabel) return;
      if (item.parentElement?.closest('.rdct-submenu--mega')) return;
      const originalId = submenu.getAttribute('id');
      const originalHidden = submenu.hidden;
      const originalInert = submenu.inert;
      const toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = 'rdct-submenu-toggle';
      toggle.setAttribute('aria-label', `${container.dataset.submenuLabel}: ${link.textContent.trim()}`);
      toggle.setAttribute('aria-expanded', 'false');
      submenu.id = `${navigation.id}-submenu-${index}`;
      toggle.setAttribute('aria-controls', submenu.id);
      submenu.hidden = true; submenu.inert = true;
      let hoverTimer;
      const close = () => {
        if (hoverTimer) { clearTimeout(hoverTimer); hoverTimer = 0; }
        toggle.setAttribute('aria-expanded', 'false'); submenu.hidden = true; submenu.inert = true;
        submenu.style.removeProperty('--rdct-menu-shift');
        submenu.style.removeProperty('--rdct-menu-height');
        submenu.style.removeProperty('--rdct-menu-block-shift');
        submenu.classList.remove('rdct-submenu--scroll');
        submenus.forEach((entry) => {
          if (!submenu.contains(entry.item)) return;
          entry.toggle.setAttribute('aria-expanded', 'false'); entry.submenu.hidden = true; entry.submenu.inert = true;
          entry.submenu.style.removeProperty('--rdct-menu-shift');
          entry.submenu.style.removeProperty('--rdct-menu-height');
          entry.submenu.style.removeProperty('--rdct-menu-block-shift');
          entry.submenu.classList.remove('rdct-submenu--scroll');
        });
      };
      const open = () => {
        if (hoverTimer) { clearTimeout(hoverTimer); hoverTimer = 0; }
        submenus.forEach((entry) => { if (entry.toggle !== toggle && !entry.item.contains(item)) entry.close(); });
        toggle.setAttribute('aria-expanded', 'true');
        submenu.inert = false;
        submenu.hidden = false;
        fitSubmenu(submenu);
      };
      const enter = event => {
        if (!['ArrowDown','ArrowUp'].includes(event.key) || event.altKey || event.ctrlKey || event.metaKey) return;
        event.preventDefault(); open();
        const links = [...submenu.querySelectorAll('a[href]')];
        (event.key === 'ArrowUp' ? links.at(-1) : links[0])?.focus({preventScroll:true});
      };
      listen(toggle, 'keydown', enter); listen(link, 'keydown', enter);
      listen(toggle, 'click', () => { if (toggle.getAttribute('aria-expanded') === 'true') close(); else open(); });
      if (hover) {
        listen(item, 'pointerenter', (event) => {if (desktop.matches && hover.matches && !wantsDrawer() && event.pointerType !== 'touch') open();});
        listen(item, 'pointerleave', (event) => {if (desktop.matches && hover.matches && !wantsDrawer() && event.pointerType !== 'touch' && !item.contains(document.activeElement)) hoverTimer = setTimeout(close,150);});
      }
      item.insertBefore(toggle, submenu);
      submenus.push({ item, submenu, toggle, close, originalId, originalHidden, originalInert });
    });
    let drawer;
    let loading;
    let intent = 0;
    let opening = false;
    let pendingScroll;
    const close = () => {
      intent += 1;
      opening = false;
      pendingScroll?.(); pendingScroll = null;
      button.removeAttribute('aria-busy');
      button.setAttribute('aria-expanded', 'false');
      drawer?.close();
      submenus.forEach((entry) => entry.close());
    };
    syncButton();
    container.dataset.enhanced = 'true';
    listen(button, 'pointerdown', (event) => {
      if (event.button !== 0 || !wantsDrawer() || opening || button.getAttribute('aria-expanded') === 'true') return;
      event.preventDefault();
      pendingScroll ||= lockPanelScroll(container.ownerDocument || document, container);
    });
    listen(button, 'pointercancel', () => { if (!opening) { pendingScroll?.(); pendingScroll = null; } });
    listen(window, 'blur', () => { if (!opening) { pendingScroll?.(); pendingScroll = null; } });
    listen(document, 'pointerup', (event) => { if (pendingScroll && !opening && !button.contains(event.target)) { pendingScroll(); pendingScroll = null; } });
    listen(button, 'click', async (event) => {
      event.preventDefault();
      if (opening || button.getAttribute('aria-expanded') === 'true') close();
      else {
        const current = ++intent;
        if (wantsDrawer()) {
          pendingScroll ||= lockPanelScroll(container.ownerDocument || document, container);
          opening = true;
          button.setAttribute('aria-busy', 'true');
          loading ??= import('./drawer.js').then(async ({ createDrawer, loadDrawerMotion }) => { const motion = await loadDrawerMotion(container).catch(() => null); if (!disposed) drawer = createDrawer(container, navigation, button, close, motion); }).catch(() => { loading = null; });
          await loading;
          if (disposed || current !== intent || !wantsDrawer()) return;
        }
        opening = false;
        button.removeAttribute('aria-busy');
        button.setAttribute('aria-expanded', 'true');
        if (wantsDrawer() && drawer) { const release = pendingScroll; pendingScroll = null; drawer.open(release); }
        else { pendingScroll?.(); pendingScroll = null; }
      }
    });
    const onKeydown = (event) => {
      if (!container.contains(event.target) && !drawer?.contains(event.target)) return;
      if (event.defaultPrevented || (event.target.closest?.('[data-rdct-menu]') && event.target.closest('[data-rdct-menu]') !== container)) return;
      const openSubmenu = submenus.filter((entry) => entry.item.contains(event.target) && !entry.submenu.hidden).pop();
      if (event.key === 'Escape' && openSubmenu) {
        event.preventDefault();
        openSubmenu.close();
        openSubmenu.toggle.focus({preventScroll:true});
        return;
      }
      if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
        event.preventDefault();
        close();
        button.focus({preventScroll:true});
      }
    };
    listen(container, 'keydown', onKeydown);
    listen(document, 'keydown', event => { if (drawer?.contains(event.target)) onKeydown(event); });
    listen(container, 'focusout', (event) => {
      submenus.forEach((entry) => { if (!entry.item.contains(event.relatedTarget)) entry.close(); });
      if (!container.contains(event.relatedTarget) && !drawer?.contains(event.relatedTarget)) close();
    });
    const onLinkClick = (event) => {
      const link = event.target.closest('a[href]');
      if ((wantsDrawer() || (!desktop.matches && !expandedMobile)) && link && (container.contains(link) || drawer?.contains(link)) && !event.defaultPrevented && event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey && link.target !== '_blank' && !link.hasAttribute('download')) {
        close();
        if (container.contains(document.activeElement)) button.focus({preventScroll:true});
      }
    };
    listen(navigation, 'click', onLinkClick);
    const panelContent = container.querySelector('.rdct-menu-panel-content');
    if (panelContent) listen(panelContent, 'click', onLinkClick);
    const panelHeader = container.querySelector('.rdct-menu-panel-header');
    if (panelHeader) listen(panelHeader, 'click', onLinkClick);
    const disposeHover = mountMenuHover(container);
    menus.push({ contains:target=>container.contains(target) || drawer?.contains(target), container, navigation, button, close, submenus, syncButton, wantsDrawer, destroy() {
      disposeHover(); close(); drawer?.destroy(); button.hidden = originalHidden;
      submenus.forEach(({ submenu, toggle, originalId, originalHidden, originalInert }) => {
        toggle.remove(); submenu.hidden = originalHidden; submenu.inert = originalInert;
        if (originalId === null || originalId === undefined) submenu.removeAttribute('id'); else submenu.setAttribute('id', originalId);
      });
      delete container.dataset.enhanced;
    } });
  });
  if (!menus.length) return () => {};
  listen(window, 'pagehide', () => menus.forEach(({ close }) => close()));
  listen(window, 'resize', () => {
    menus.forEach(({ submenus }) => submenus.forEach(({ submenu }) => { if (!submenu.hidden) fitSubmenu(submenu); }));
  });
  listen(document, 'click', (event) => {
    menus.forEach(({ contains, close }) => { if (!contains(event.target)) close(); });
  });
  const visible = (node) => node && !node.hidden && node.getClientRects().length > 0;
  const reset = () => {
    const active = document.activeElement;
    const focused = menus.find(({ container }) => container.contains(active));
    let focusTarget;
    let relocate = false;
    menus.forEach((entry) => {
      const { navigation, button, close, submenus, syncButton, wantsDrawer } = entry;
      const owner = submenus.find((entry) => entry.submenu.contains(active));
      close();
      syncButton();
      if (focused !== entry) return;
      if ((!desktop.matches || wantsDrawer()) && visible(button)) focusTarget = button;
      else if (owner && visible(owner.toggle)) focusTarget = owner.toggle;
      else if (!visible(active)) relocate = true;
    });
    if (relocate) focusTarget = menus.flatMap(({ button, navigation }) => [button, navigation.querySelector('a[href]')]).find(visible) || document.getElementById('main-content');
    focusTarget?.focus({preventScroll:true});
  };
  [desktop, laptop, wide].forEach((media) => listen(media, 'change', reset));
  return () => {
    if (disposed) return;
    disposed = true;
    listeners.forEach((remove) => remove());
    menus.forEach(({ destroy }) => destroy());
  };
}
