export function nativeIconValue(value) {
  const name = typeof value === 'string' ? value : localIconName(value);
  return name && /^[a-z][a-z0-9-]{0,70}$/.test(name)
    ? { library: 'rdct-local', value: `rdct-library rdct-icon-${name}` }
    : { library: '', value: '' };
}

export function localIconName(value, names) {
  if (typeof value === 'string') return !names || names.includes(value) || value === '' ? value : undefined;
  if (!value || typeof value !== 'object') return undefined;
  if (value.value === '' && (!value.library || value.library === 'rdct-local')) return '';
  if (value.library !== 'rdct-local' || typeof value.value !== 'string') return undefined;
  const match = value.value.match(/^rdct-library rdct-icon-([a-z][a-z0-9-]{0,70})$/);
  return match && (!names || names.includes(match[1])) ? match[1] : undefined;
}

export function ensureLocalIconLibrary(editor, definition) {
  const libraries = editor?.config?.icons?.libraries;
  if (!Array.isArray(libraries) || definition?.name !== 'rdct-local' || !Array.isArray(definition.icons) || !definition.icons.length) return false;
  if (!libraries.some((library) => library?.name === 'rdct-local')) {
    libraries.push({ ...definition, icons: definition.icons.slice() });
  }
  return true;
}

const loadingLibraries = new WeakMap();
export function prepareNativeIconLibraries(editor, definition, timeout = 10000) {
  const manager = editor?.iconManager;
  if (!manager?.library?.initIconType || !ensureLocalIconLibrary(editor, definition)) return Promise.reject(new Error('Icon libraries unavailable'));
  if (loadingLibraries.has(manager)) return loadingLibraries.get(manager);
  const pending = (async () => {
    const deadline = Date.now() + timeout;
    for (const tab of editor.config.icons.libraries.slice()) {
      if (['all','recommended'].includes(tab.name) || (!tab.icons && !tab.fetchJson)) continue;
      const remaining = deadline - Date.now();
      if (remaining <= 0) throw new Error('Icon library timed out');
      await new Promise((resolve,reject) => {
        const timer = setTimeout(() => reject(new Error('Icon library timed out')),remaining);
        try {
          manager.library.initIconType(tab,(library) => {
            if (library?.name !== tab.name) return;
            clearTimeout(timer); resolve(library);
          });
        } catch (error) { clearTimeout(timer); reject(error); }
      });
    }
  })().finally(() => loadingLibraries.delete(manager));
  loadingLibraries.set(manager,pending);
  return pending;
}

export function nativeIconManagerView(value, onChange, names, onInvalid = () => {}) {
  return {
    model: { get: (key) => key === 'recommended' ? { 'rdct-local': names } : false },
    getControlValue: () => nativeIconValue(value),
    setValue: (icon) => {
      const next = localIconName(icon, names);
      if (next === undefined) { onInvalid(); return; }
      onChange(next);
    },
    applySavedValue: () => {},
  };
}
