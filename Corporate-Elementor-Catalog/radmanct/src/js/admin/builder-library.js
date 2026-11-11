import { ButtonControl, SelectControl } from '../../../../shared/admin/controls.js';
import { request } from './builder-api.js';
import { layoutAssignment } from './builder-selection.js';
import { createBackupImporter, downloadBackup } from './builder-backup.js';
import { Translations } from './builder-translations.js';

const { createElement: h, useState, useRef, useEffect } = window.wp.element;
const config = window.rdctBuilder;
const { labels } = config;

export function Library({ stored, layout, dirty, busy = false, run, change, onNew, mode = 'layouts' }) {
  const input = useRef(null);
  const importButton = useRef(null);
  const [restore, setRestore] = useState({ phase: 'idle' });
  const importer = useRef(null);
  if (!importer.current) importer.current = createBackupImporter(request, config.kind, config.origin);
  const current = useRef(null);
  current.current = { id: stored.id, revision: stored.data.revision, layout, busy };
  useEffect(() => { importer.current.cancel(true); }, [stored.id, stored.data.revision, layout, busy, mode]);
  useEffect(() => () => importer.current.cancel(), []);
  const phase = restore.id === stored.id && restore.revision === stored.data.revision && !busy ? restore.phase : 'idle';
  const restoring = phase === 'reading' || phase === 'validating';
  const action = (...args) => { importer.current.cancel(true); run(...args); };
  const button = (text, onClick, disabled = false, extra = {}) => h(ButtonControl, { type: 'button', onClick, disabled, ...extra }, text);
  function create(blank) {
    if (blank && dirty && !confirm(labels.discardConfirm)) return;
    onNew?.(blank ? { version: 1, enabled: false, rows: [] } : layout);
  }
  function exportLayout() {
    try { downloadBackup(layout, config.kind, config.origin); setRestore({ phase: 'idle' }); }
    catch { setRestore({ phase: 'export-error', error: labels.exportError, id: stored.id, revision: stored.data.revision }); }
  }
  function importLayout(event) {
    const file = event.target.files[0];
    event.target.value = '';
    if (!file || busy) return;
    const snapshot = current.current;
    return importer.current.load(file, stored.id, {
      isCurrent: () => !current.current.busy && current.current.id === snapshot.id && current.current.revision === snapshot.revision && current.current.layout === snapshot.layout,
      confirm: () => confirm(labels.importConfirm), apply: change, errorMessage: labels.importError,
      report: (error) => setRestore({ phase: 'error', error, id: snapshot.id, revision: snapshot.revision }),
      status: (value) => setRestore({ phase: value, id: snapshot.id, revision: snapshot.revision }),
    });
  }
  return h('section', { className: 'rdct-bld-library' },
    mode==='layouts' && h('label', null, labels.library, h(SelectControl, { value: stored.id, options: stored.layouts.map((entry) => [entry.id, entry.title]), onChange: (id) => {
      if (!dirty || confirm(labels.discardConfirm)) action('read', {}, id);
    } })),
    mode==='layouts' && h('div', { className: 'rdct-bld-library__actions' },
      button(labels.newLayout, () => create(true), stored.layouts.length >= 13),
      button(labels.copyLayout, () => create(false), stored.layouts.length >= 13),
      button(labels.renameLayout, () => document.getElementById('rdct-builder-title')?.focus()),
      button(labels.deleteLayout, () => { if (confirm(labels.deleteConfirm)) action('delete'); }, stored.creating || dirty)),
    mode==='backups' && h('div', { className: 'rdct-bld-library__actions' },
      button(labels.exportLayout, exportLayout, restoring), button(labels.importLayout, () => input.current.click(), false, { ref: importButton }),
      restoring && button(labels.cancelImport, () => { importer.current.cancel(true); importButton.current?.focus(); }),
      h('input', { ref: input, type: 'file', accept: '.json,application/json', hidden: true, onChange: importLayout })),
    mode==='translations' && h(Translations, { stored, dirty, busy, run }),
    ['error', 'export-error'].includes(phase) && h('p', { role: 'alert' }, restore.error || labels.restore_error),
    h('p', { role: 'status', 'aria-live': 'polite', 'aria-atomic': true }, ['idle', 'error', 'export-error'].includes(phase) ? '' : labels[`restore_${phase}`]));
}

export function Assignment({ stored, dirty, run }) {
  const [language, setLanguage] = useState('all');
  const state = layoutAssignment(stored, language);
  return h('section', { className: 'rdct-bld-library', 'aria-label': labels.assignment },
    h('p', { role: 'status' }, labels[state.status]),
    h('div', { className: 'rdct-bld-editor__actions' },
      h('label', null, labels.language, h(SelectControl, { value: language, onChange: setLanguage, options: config.languages.map((entry) => [entry.id, entry.title]) })),
      h(ButtonControl, { type: 'button', disabled: dirty || !stored.data.layout.enabled || state.direct === stored.id, onClick: () => run('default', { language }) }, language === 'all' ? labels.siteDefault : labels.languageDefault),
      language !== 'all' && h(ButtonControl, { type: 'button', disabled: dirty || !state.direct, onClick: () => run('inherit', { language }) }, labels.inheritDefault)),
    h('p', { className: 'description' }, labels.assignmentHint));
}
