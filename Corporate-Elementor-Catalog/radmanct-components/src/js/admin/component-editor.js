import { prioritizeRadmanPanel } from './elementor-panel.js';
import { createLibraryDialog } from './component-dialog.js';

export function editorReady(editor, commands) {
  try { return !!(typeof commands?.run === 'function' && editor?.$previewContents?.[0]?.readyState === 'complete' && editor?.getPreviewContainer?.() && editor?.documents?.getCurrentId?.()); }
  catch { return false; }
}

if (typeof window !== 'undefined' && typeof document !== 'undefined') {
  let attached;
  let initialized = false;
  let loading;
  let controller;
  let generation = 0;
  const linkedPreviews = new WeakSet();
  const launcher = document.querySelector('[data-library-open]');
  const label = launcher?.textContent;
  let dialog;
  const closed = () => {
    generation++;
    controller?.abort();
    loading = null;
    dialog?.busy(false);
    if (launcher) { launcher.disabled = false; launcher.removeAttribute('aria-busy'); }
    launcher?.setAttribute('aria-expanded', 'false');
    dialog?.content.querySelector('[data-rdct-library]')?.dispatchEvent(new Event('rdct:library-close'));
    if (launcher?.rdctLibraryOpener?.isConnected) launcher.rdctLibraryOpener.focus({preventScroll:true});
  };
  const modal = () => dialog ||= createLibraryDialog(window.elementorCommon?.dialogsManager, {label, closeLabel:launcher?.dataset.libraryCloseLabel, onHide:closed});
  launcher?.setAttribute('aria-haspopup', 'dialog');
  launcher?.setAttribute('aria-expanded', 'false');
  window.addEventListener('pagehide', () => { dialog?.hide(); controller?.abort(); });
  const shortcut = () => {
    const preview = window.elementor?.$previewContents?.[0];
    if (!preview || !launcher) return;
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'elementor-add-section-area-button';
    button.dataset.rdctLibraryShortcut = 'true';
    button.style.cssText = 'border-radius:50%;width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;padding:10px;border:0;background:#235a50;color:#fff;vertical-align:middle';
    button.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 20V4h7a4.5 4.5 0 0 1 0 9H6m6 0 6 7"/></svg>';
    button.title = label;
    button.setAttribute('aria-label', label);
    const template = document.getElementById('tmpl-elementor-add-section');
    if (template && !template.textContent.includes('data-rdct-library-shortcut')) {
      template.textContent = template.textContent.replace('<div class="elementor-add-section-drag-title', `${button.outerHTML}<div class="elementor-add-section-drag-title`);
    }
    preview.querySelectorAll('.elementor-add-section-drag-title').forEach((title) => {
      if (!title.parentElement.querySelector('[data-rdct-library-shortcut]')) title.before(button.cloneNode(true));
    });
    if (!linkedPreviews.has(preview)) {
      linkedPreviews.add(preview);
      preview.addEventListener('click', (event) => {
        const opener = event.target.closest?.('[data-rdct-library-shortcut]');
        if (opener) { event.preventDefault(); event.stopPropagation(); launcher.rdctLibraryOpener = opener; launcher.click(); }
      }, true);
    }
  };
  const reveal = () => {
    prioritizeRadmanPanel(window.elementor);
    if (!editorReady(window.elementor, window.$e)) return;
    const launcher = document.querySelector('[data-library-open]');
    if (launcher && window.elementorCommon?.dialogsManager?.createWidget) { shortcut(); }
  };
  launcher?.addEventListener('click', async (event) => {
    event.stopImmediatePropagation();
    if (!editorReady(window.elementor, window.$e)) return;
    if (!modal()) return;
    if (!dialog.open) dialog.show();
    launcher.setAttribute('aria-expanded', 'true');
    if (initialized) { dialog.content.querySelector('[data-library-search]')?.focus({preventScroll:true}); return; }
    if (loading) return;
    const target = dialog.content;
    target.textContent = launcher.dataset.libraryLoading;
    dialog.busy(true);
    const requestController = new AbortController();
    controller = requestController;
    const ticket = ++generation;
    launcher.disabled = true;
    launcher.setAttribute('aria-busy', 'true');
    loading = (async () => {
      const url = new URL(launcher.dataset.libraryUrl, window.location.href);
      if (url.origin !== window.location.origin) throw new Error('Invalid library origin');
      const documentId = window.elementor.documents.getCurrentId();
      const body = new URLSearchParams({ action: 'radman_component_library', _ajax_nonce: launcher.dataset.libraryNonce, post_id: documentId });
      const response = await fetch(url, { method: 'POST', credentials: 'same-origin', body, signal:requestController.signal });
      const result = await response.json();
      if (!response.ok || !result.success || typeof result.data?.html !== 'string') throw new Error('Invalid library response');
      if (!editorReady(window.elementor, window.$e) || window.elementor.documents.getCurrentId() !== documentId) throw new Error('Document changed');
      if (requestController.signal.aborted || ticket !== generation || !dialog.open) throw new Error('Library closed');
      target.innerHTML = result.data.html;
      const { initializeLibrary } = await import('./components.js');
      if (requestController.signal.aborted || ticket !== generation || !dialog.open) throw new Error('Library closed');
      const root = target.querySelector('[data-rdct-library][data-editor]');
      if (!root) throw new Error('Missing library');
      initializeLibrary(root);
      initialized = true;
      root.querySelector('[data-library-search]')?.focus({preventScroll:true});
    })();
    try {
      await loading;
      if (ticket !== generation) return;
      launcher.textContent = label;
      if (launcher.rdctLibraryOpener?.isConnected) {
        launcher.rdctLibraryOpener.title = label;
        launcher.rdctLibraryOpener.setAttribute('aria-label', label);
      }
    } catch { if (!requestController.signal.aborted && ticket === generation) { target.textContent = launcher.dataset.libraryError; launcher.textContent = launcher.dataset.libraryError; if (launcher.rdctLibraryOpener?.isConnected) { launcher.rdctLibraryOpener.title = launcher.dataset.libraryError; launcher.rdctLibraryOpener.setAttribute('aria-label',launcher.dataset.libraryError); } } }
    finally { if (ticket === generation) { loading = null; launcher.disabled = false; launcher.removeAttribute('aria-busy'); dialog.busy(false); } }
  });
  const attach = () => {
    const editor = window.elementor;
    if (!editor) return;
    if (attached !== editor) {
      attached = editor;
      editor.on?.('preview:loaded', reveal);
      editor.on?.('document:loaded', reveal);
    }
    reveal();
  };
  window.addEventListener('elementor/init', attach);
  window.addEventListener('elementor/loaded', attach);
  window.__elementorEditorV1LoadingPromise?.then(attach);
  attach();
}
