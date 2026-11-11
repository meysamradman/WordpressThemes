import {createBuilderRequest} from './builder-api.js';
import {ButtonControl, SelectControl, LoadingStatus} from '../../../../shared/admin/controls.js';

const {createElement: h, useState, useEffect, useRef} = window.wp.element;

export async function saveBuilderSections(savers) {
  for (const kind of ['header','footer']) { if (savers[kind] && await savers[kind]() === false) return false; }
  return true;
}

export function assignmentChanges(saved, draft) {
  return [...new Set([...Object.keys(saved), ...Object.keys(draft)])]
    .filter(language => saved[language] !== draft[language])
    .map(language => ({language, id:draft[language]}));
}

export function applyAssignmentChanges(defaults, changes) {
  const next = {...defaults};
  for (const {language, id} of changes) { if (id === undefined) delete next[language]; else next[language] = id; }
  return next;
}

const selection = (defaults, language) => defaults[language] ?? (language === 'all' ? 'default' : '');

function BuilderKindSettings({kind, language, config, onDirty, registerSave, onLoading}) {
  const {labels} = config;
  const [data, setData] = useState(null);
  const [saved, setSaved] = useState({});
  const [draft, setDraft] = useState({});
  const [operation, setOperation] = useState('load');
  const busy = !!operation;
  const [error, setError] = useState('');
  const alive = useRef(true), pending = useRef(false), controller = useRef(null), latest = useRef(null), api = useRef(null), epoch = useRef(0);
  if (!api.current) api.current = createBuilderRequest({...config, kind});
  latest.current = {saved, draft};
  const dirty = assignmentChanges(saved, draft).length > 0;

  const load = async () => {
    if (pending.current) return;
    const task = ++epoch.current;
    pending.current = true; setOperation('load'); setError('');
    try {
      const result = await api.current('assignments', 'default', {}, controller.current.signal);
      if (!alive.current || task !== epoch.current) return;
      const edits = assignmentChanges(latest.current.saved, latest.current.draft);
      const baseline = {...result.defaults};
      const next = applyAssignmentChanges(baseline, edits);
      latest.current = {saved:baseline, draft:next};
      setData(result); setSaved(baseline); setDraft(next);
    } catch (failure) { if (alive.current && task === epoch.current && failure.name !== 'AbortError') setError(failure.message); }
    finally { if (alive.current && task === epoch.current) {pending.current = false; setOperation('');} }
  };

  const save = async () => {
    if (pending.current) return false;
    const changes = assignmentChanges(latest.current.saved, latest.current.draft);
    if (!changes.length) return true;
    const task = ++epoch.current;
    pending.current = true; setOperation('save'); setError('');
    try {
      for (let index = 0; index < changes.length; ++index) {
        const change = changes[index];
        const result = await api.current(change.id === undefined ? 'inherit' : 'default', change.id ?? 'default', {language:change.language}, controller.current.signal);
        if (!alive.current || task !== epoch.current) return false;
        const baseline = {...result.defaults};
        const next = applyAssignmentChanges(baseline, changes.slice(index + 1));
        latest.current = {saved:baseline, draft:next};
        setSaved(baseline); setDraft(next);
      }
      return true;
    } catch (failure) { if (alive.current && task === epoch.current && failure.name !== 'AbortError') setError(failure.message); return false; }
    finally { if (alive.current && task === epoch.current) {pending.current = false; setOperation('');} }
  };

  useEffect(() => {
    alive.current = true; controller.current = new AbortController(); load();
    return () => {alive.current = false; ++epoch.current; pending.current = false; controller.current.abort();};
  }, []);
  useEffect(() => {registerSave(kind, save); return () => registerSave(kind, null);}, []);
  useEffect(() => {onDirty(kind, dirty);}, [dirty]);
  useEffect(() => {onLoading(kind, operation === 'load');}, [kind, operation, onLoading]);

  const value = selection(draft, language);
  const options = (data?.layouts || []).map(item => [item.id,
    `${item.title}${item.language ? ` (${config.languages.find(entry=>entry.id===item.language)?.title || item.language})` : ''}${item.enabled ? '' : ` - ${labels.layoutInactive}`}`,
    !item.enabled]);
  options.unshift(['none', labels.none]);
  if (language !== 'all') options.unshift(['', labels.inheritDefault]);
  if (data && !options.some(([id]) => id === value)) options.unshift([value, labels.unavailableLayout, true]);
  const change = id => setDraft(current => {
    const next = {...current};
    if (id === '' || (id === selection(saved, language) && saved[language] === undefined)) delete next[language];
    else next[language] = id;
    return next;
  });
  const canEdit = value && data?.layouts.some(item => item.id === value);
  return h('section', {className:'rdct-builder-site-settings', 'aria-busy':busy},
    h('h3',null,labels[`${kind}Selection`]),
    data && h('fieldset',{disabled:busy},
      h('label',null,labels.layoutTitle,h(SelectControl,{value,options,onChange:change})),
      h('div',{className:'rdct-stg__actions'},
        canEdit && h('a',{className:'rdct-btn',href:`${config.editorUrls[kind]}&layout=${encodeURIComponent(value)}`},labels.editLayout),
        h('a',{className:'rdct-btn',href:config.editorUrls[kind]},labels.library),
        h(ButtonControl,{type:'button',disabled:busy,onClick:load},labels.refreshLayouts))),
    !data && !busy && h(ButtonControl,{type:'button',onClick:load},labels.retryFields),
    error && h('p',{role:'alert'},error));
}

export function BuilderSiteSettings({config, onDirty, registerSave, saving = false}) {
  const [language, setLanguage] = useState(config.activeLanguage || 'all');
  const [loading, setLoading] = useState({header:true,footer:true});
  const reportLoading = useRef((kind, value) => setLoading(previous => previous[kind] === value ? previous : {...previous,[kind]:value}));
  const states = useRef({}), savers = useRef({});
  const register = useRef((kind, save) => {if(save)savers.current[kind]=save;else delete savers.current[kind];});
  useEffect(() => {registerSave(() => saveBuilderSections(savers.current));return () => registerSave(null);}, [registerSave]);
  const report = (kind, value) => {states.current[kind]=value;onDirty(Object.values(states.current).some(Boolean));};
  useEffect(() => () => onDirty(false), []);
  return h('div',null,
    !saving && Object.values(loading).some(Boolean) && h(LoadingStatus,{label:config.labels.loading}),
    config.languages.length > 1 && h('div',{className:'rdct-builder-site-settings'},h('label',null,config.labels.language,
      h(SelectControl,{value:language,options:config.languages.map(entry=>[entry.id,entry.title]),onChange:setLanguage}))),
    ...['header','footer'].map(kind => h(BuilderKindSettings,{key:kind,kind,language,config,onDirty:report,registerSave:register.current,onLoading:reportLoading.current})));
}
