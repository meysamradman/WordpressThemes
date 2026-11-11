import { ButtonControl, SelectControl } from '../../../../shared/admin/controls.js';


const { createElement: h, useState, useEffect } = window.wp.element;
const { labels } = window.rdctBuilder;

export function Translations({ stored, dirty, busy, run }) {
  const state = stored.translation;
  const [source, setSource] = useState(state?.source || '');
  const [existing, setExisting] = useState({});
  useEffect(() => { setSource(state?.source || ''); setExisting({}); }, [stored.id, state?.source]);
  if (!state?.items?.length) return null;
  const unavailable = dirty || busy;
  const linked = new Set(state.items.map((entry) => entry.id).filter(Boolean));
  const options = [['', labels.translationCopy], ...stored.layouts.filter((entry) => entry.id !== stored.id && !linked.has(entry.id)).map((entry) => [entry.id, entry.title])];
  return h('section', { className: 'rdct-bld-library', 'aria-label': labels.translations },
    h('strong', null, labels.translations),
    h('label', null, labels.originalLanguage, h(SelectControl, { value: source, disabled: unavailable || state.bound, options: state.items.map((entry) => [entry.code, entry.title]), onChange: setSource })),
    ...state.items.filter((entry) => entry.code !== source).map((entry) => h('div', { key: entry.code, className: 'rdct-bld-editor__actions' },
      h('span', null, entry.title),
      entry.id ? h(ButtonControl, { type: 'button', disabled: unavailable, onClick: () => run('read', {}, entry.id) }, labels.editTranslation)
        : h('div', null,
          h(SelectControl, { value: existing[entry.code] || '', disabled: unavailable, 'aria-label': `${labels.translationLayout}: ${entry.title}`, options, onChange: (id) => setExisting((value) => ({ ...value, [entry.code]: id })) }),
          h(ButtonControl, { type: 'button', disabled: unavailable, onClick: () => run('translate', { source_language: source, language: entry.code, revision: stored.data.revision, existing: existing[entry.code] || '' }) }, existing[entry.code] ? labels.connectTranslation : labels.addTranslation)))),
    dirty && h('p', { role: 'status' }, labels.translationSaveFirst));
}
