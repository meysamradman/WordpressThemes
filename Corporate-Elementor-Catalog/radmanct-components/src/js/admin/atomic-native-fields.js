const wrap = (type, value) => ({ $$type: type, value });

export function nativeRichValue(value) {
  if (value?.$$type && value.$$type !== 'string') return value;
  const text = value?.$$type === 'string' ? value.value : value;
  return wrap('escaped-html', typeof text === 'string' ? text : '');
}

export function nativeImageValue(value) {
  if (value?.$$type && value.$$type !== 'number') return value;
  const id = value?.$$type === 'number' ? value.value : value;
  if (!Number.isSafeInteger(id) || id <= 0) return null;
  return wrap('image', { src: wrap('image-src', { id: wrap('image-attachment-id', id) }), size: wrap('string', 'large') });
}

export function nativeEditorRow(row, fields) {
  const value = { ...row.value };
  for (const [key, field] of Object.entries(fields)) {
    if (field.rich) value[key] = nativeRichValue(value[key] ?? field.default);
    else if (field.media) value[key] = nativeImageValue(value[key]);
  }
  return { ...row, value };
}

export function registerNativeRichText(registry, controls, element, props) {
  if (registry.get('rdct-rich-text') || !controls.InlineEditingControl) return;
  const h = element.createElement;
  function RichTextControl({ context } = {}) {
    const bound = controls.useBoundProp();
    const value = nativeRichValue(bound.value ?? bound.baseValue ?? bound.propType.default);
    return h(controls.PropProvider, {
      propType: { kind: 'object', shape: { content: bound.propType } },
      value: { content: value }, isDisabled: () => bound.disabled ?? bound.isDisabled?.(bound.propType),
      setValue: (next, options, meta) => bound.setValue(next.content, options, meta),
    }, h(controls.PropKeyProvider, { bind: 'content' }, h(controls.InlineEditingControl, { context })));
  }
  registry.register('rdct-rich-text', RichTextControl, 'full', props.escapedHtmlPropTypeUtil);
}

export function createNativeTemplateSelect(controls, h) {
  return function TemplateSelect({ options = [], ariaLabel }) {
    const bound = controls.useBoundProp();
    const raw = bound.value ?? bound.baseValue ?? bound.propType.default;
    const value = String(raw?.value ?? 0);
    const choices = options.map((option) => ({ ...option, value: String(option.value) }));
    const allowed = new Set(choices.map((option) => option.value));
    const disabled = () => bound.disabled ?? bound.isDisabled?.(bound.propType);
    return h(controls.PropProvider, {
      propType: { kind: 'object', shape: { selection: bound.propType } },
      value: { selection: wrap('string', value) }, isDisabled: disabled,
      setValue: (next, settings, meta) => {
        const selected = next.selection?.value ?? '0';
        if (disabled() || !allowed.has(selected) || !/^\d+$/.test(selected) || !Number.isSafeInteger(Number(selected))) return;
        bound.setValue(wrap('number', Number(selected)), settings, meta);
      },
    }, h(controls.PropKeyProvider, { bind: 'selection' }, h(controls.SelectControl, { options: choices, ariaLabel })));
  };
}

export function registerNativeTemplateSelect(registry, controls, element, props) {
  if (registry.get('rdct-template-select') || !controls.SelectControl) return;
  registry.register('rdct-template-select', createNativeTemplateSelect(controls, element.createElement), 'full', props.numberPropTypeUtil);
}
