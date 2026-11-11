import { ButtonControl, LoadingStatus } from '../../../../shared/admin/controls.js';
import { SectionNavigation } from '../../../../shared/admin/section-navigation.js';
import { InputField, SelectField, FontFaces, ToggleField, ColorSetting } from './fields.js';
import { saveSettings, mergeSaved } from './settings-save.js';
import { primaryFontGroup } from './settings-fonts.js';
import { resetSettings } from './settings-reset.js';
import { CursorFields, cursorKeys } from './cursor-fields.js';
import { ContentLayoutFields } from './layout-fields.js';
import { AnalyticsFields } from './analytics-fields.js';

const { createElement: h, useState, useEffect, useRef } = window.wp.element;
const sections = {
  analytics: ['analytics_enabled', 'analytics_measurement_id'],
  custom_css: ['custom_css', 'custom_css_desktop', 'custom_css_laptop', 'custom_css_tablet', 'custom_css_mobile'],
  custom_js: ['custom_js'],
  identity: ['svg_uploads'],
  typography_primary: [], typography_secondary: [], typography_loading: ['font_language', 'mixed_language_fonts', 'block_google_fonts'],
  design: ['brand_color','color_scheme','dark_background','dark_surface','dark_text','dark_muted','dark_border'],
  layout: [],
  page_title: ['page_title','page_title_typography'],
  blog_archive: [], blog_single: [], catalog_archive: [], catalog_single: [], portfolio_archive: [], portfolio_single: [],
  blog_typography: ['blog_typography'],
  portfolio_typography: ['portfolio_typography'], catalog_typography: ['catalog_typography'],
  header_footer: [],
  motion: ['scroll_reveal', 'smooth_scroll', 'smooth_duration', 'custom_scrollbar', 'scrollbar_width', 'scrollbar_thumb', 'scrollbar_track'],
  cursor: cursorKeys,
  progress: ['scroll_progress', 'scroll_progress_height', 'scroll_progress_color', 'scroll_progress_track'],
  preloader: ['preloader', 'preloader_style', 'preloader_background', 'preloader_background_secondary', 'preloader_color'],
};

export function SettingsApp({ config }) {
  const { labels } = config;
  const [active, setActive] = useState(() => {
    const hash = window.location.hash.replace('#rdct-', '');
    const key = hash === 'typography' ? 'typography_primary' : hash;
    return Object.hasOwn(sections, key) && (!key.startsWith('custom_') || config.canEditCode) ? key : 'identity';
  });
  const [values, setValues] = useState(config.values);
  const [savedValues, setSavedValues] = useState(config.values);
  const saveAction = useRef(null);
  const [builderDirty, setBuilderDirty] = useState(false);
  const [builderBusy,setBuilderBusy]=useState(false);
  const [builderMessage,setBuilderMessage]=useState('');
  const builderSave=useRef(null);
  const builderPending=useRef(false);
  const registerBuilderSave=useRef(save=>{builderSave.current=save;});
  const [panels, setPanels] = useState({});
  const [panelError, setPanelError] = useState('');
  const BuilderSettings = panels.header_footer;
  const TypographyFields = panels.typography;
  const ColorSettingsFields = panels.design;
  const ContentViewFields = panels.content;
  const PageTitleSettings = panels.page_title;
  const ContentTypographyFields = panels.content_typography;
  const contentTypographyView = /^(blog|portfolio|catalog)_typography$/.test(active);
  const contentView = /^(blog|catalog|portfolio)_(archive|single)$/.test(active);
  const typographyView = active.startsWith('typography_');
  const primaryGroup = primaryFontGroup(config, values);
  const fontGroup = active === 'typography_secondary' ? (primaryGroup === 'persian' ? 'latin' : 'persian') : primaryGroup;
  const panelKey = contentTypographyView ? 'content_typography' : contentView ? 'content' : typographyView ? 'typography' : active;
  useEffect(() => {
    const loaders = {page_title: () => import('./page-title-settings.js'),content_typography: () => import('./content-typography-fields.js'), content: () => import('./content-view-fields.js'), design: () => import('./color-settings-fields.js'), typography: () => import('./typography-fields.js'), header_footer: () => import('./builder-site-settings.js')};
    if (!loaders[panelKey] || panels[panelKey]) return;
    let current = true; setPanelError('');
    loaders[panelKey]().then((module) => {if (current) setPanels((values) => ({...values, [panelKey]: module.PageTitleSettings || module.ContentViewFields || module.ContentTypographyFields || module.BuilderSiteSettings || module.TypographyFields || module.ColorSettingsFields}));}).catch(() => {if (current) setPanelError(panelKey);});
    return () => {current = false;};
  }, [panelKey, panels]);
  const [CodeFields, setCodeFields] = useState(null);
  const [codeError, setCodeError] = useState(false);
  useEffect(() => {
    if (!active.startsWith('custom_') || !config.canEditCode || CodeFields) return;
    let current = true;
    import('./custom-code-fields.js').then((module) => { if (current) setCodeFields(() => module.CustomCodeFields); }).catch(() => { if (current) setCodeError(true); });
    return () => { current = false; };
  }, [active, CodeFields]);
  const dirty = JSON.stringify(values) !== JSON.stringify(savedValues);
  const change = (key, value) => { setBuilderMessage(''); setValues((current) => ({ ...current, [key]: typeof value === 'function' ? value(current[key] || {}) : value })); };
  const color = (name, label) => h(ColorSetting, { key: name, name, label, value: values[name], onChange: change, fallback: config.defaults[name], resetValue: config.defaults[name], resetLabel: labels.resetColor });
  const saveCurrent=async()=>{
    if(builderPending.current || !(dirty || builderDirty))return;
    builderPending.current=true;
    setBuilderBusy(true);setBuilderMessage(labels.saving);
    const submitted=values;
    try {
      if(builderDirty && (!builderSave.current || await builderSave.current()===false))throw new Error(labels.saveError);
      if(dirty){
        const saved=await saveSettings(config,submitted);
        setSavedValues(saved);setValues(current=>mergeSaved(current,submitted,saved));
      }
      setBuilderMessage(labels.saved);
    } catch(error) {setBuilderMessage(error?.message || labels.saveError);}
    finally {builderPending.current=false;setBuilderBusy(false);}
  };
  saveAction.current=saveCurrent;
  useEffect(()=>{
    const form=document.querySelector('.rdct-stg-form');
    const submit=event=>{event.preventDefault();saveAction.current?.();};
    form?.addEventListener('submit',submit);
    return()=>form?.removeEventListener('submit',submit);
  },[]);

  useEffect(() => {
    const path = `${window.location.pathname}${window.location.search}#rdct-${active}`;
    window.history.replaceState(null, '', path);
    const referer = document.querySelector('.rdct-stg-form input[name="_wp_http_referer"]');
    if (referer) referer.value = path;
  }, [active]);

  useEffect(() => {
    const beforeUnload = (event) => {
      if (!dirty && !builderDirty) return;
      event.preventDefault();
      event.returnValue = '';
    };
    window.addEventListener('beforeunload', beforeUnload);
    return () => {
      window.removeEventListener('beforeunload', beforeUnload);
    };
  }, [dirty,builderDirty]);

  const loadingPanel = !builderBusy && h(LoadingStatus,{label:config.builder.labels.loading});
  const loadError = () => h('div', {role:'alert'}, h('p', null, labels.panelLoadError), h(ButtonControl, {onClick: () => window.location.reload()}, labels.reloadPanel));
  const reset = (all) => {
    if (!window.confirm(all ? labels.resetAllConfirm : labels.resetSectionConfirm)) return;
    setValues(current => resetSettings(current, config.defaults, all ? null : active, sections[active], config.canEditCode, primaryFontGroup(config, current)));
  };

  const groups = [
    { id: 'general', label: labels.general, icon: 'admin-settings', keys: ['identity', 'analytics'] },
    { id: 'appearance', label: labels.appearanceGroup, icon: 'art', keys: ['design', 'layout', 'page_title', 'header_footer'] },
    { id: 'typography', label: labels.typography, icon: 'editor-textcolor', keys: ['typography_primary', 'typography_secondary', 'typography_loading'] },
    ...['blog', 'portfolio', 'catalog'].map(scope => ({ id: scope, label: labels[scope], icon: scope === 'blog' ? 'admin-post' : scope === 'portfolio' ? 'portfolio' : 'products', keys: [`${scope}_typography`, `${scope}_archive`, `${scope}_single`] })),
    { id: 'effects', label: labels.effects, icon: 'admin-customizer', keys: ['motion', 'cursor', 'progress', 'preloader'] },
    ...(config.canEditCode ? [{ id: 'code', label: labels.customCode, icon: 'editor-code', keys: ['custom_css', 'custom_js'] }] : []),
  ].map((group) => ({ ...group, items: group.keys.map((id) => ({ id, label: id.endsWith('_typography') ? labels.typography : id.endsWith('_archive') ? labels.archiveView : id.endsWith('_single') ? labels.singleView : labels[id] })) }));

  return h('div', { className: 'rdct-stg' },
    h(FontFaces, { config, values }),
    config.canEditCode && Object.keys(config.defaults).filter((key) => key.startsWith('custom_css') || key === 'custom_js').map((key) => h('input', { key, type: 'hidden', name: `radman_settings[${key}]`, value: values[key] })),
    h('aside', { className: 'rdct-stg__sidebar rdct-adm-sidebar' },
      h('div', { className: 'rdct-stg__brand' },
        h('img', { src: config.logo, alt: labels.themeName, width: 72, height: 44 }),
        h('small', { dir: 'ltr' }, `v${config.version}`),
      ),
      h(SectionNavigation, { groups, value: active, onChange: (next) => {if (!builderPending.current && (!builderDirty || confirm(config.builder.labels.discardConfirm))) setActive(next);}, label: labels.title })),
    h('div', { className: 'rdct-stg__body' },
      h('header', { className: 'rdct-stg__toolbar' },
        h('div', null, h('h2', null, labels.title), h('span', { className: `rdct-stg__status${dirty || builderDirty ? ' is-dirty' : ''}${builderBusy ? ' rdct-loading' : ''}`, role: 'status' }, (builderMessage === labels.saved && (dirty || builderDirty) ? labels.unsaved : builderMessage) || (dirty || builderDirty ? labels.unsaved : labels.upToDate))),
        h('div', { className: 'rdct-stg__actions' },
          !['identity','header_footer'].includes(active) && h(ButtonControl, { icon: 'image-rotate', onClick: () => reset(false) }, labels.resetSection),
          active !== 'header_footer' && h(ButtonControl, { icon: 'backup', onClick: () => reset(true) }, labels.resetAll),
          h(ButtonControl, { type: 'submit', icon: 'saved', variant: 'primary', disabled: builderBusy || !(dirty || builderDirty), 'aria-busy': builderBusy }, labels.save),
        ),
      ),
      active === 'header_footer' && h('section', {id:'rdct-panel-header_footer',role:'tabpanel','aria-labelledby':'rdct-tab-header_footer',className:'rdct-stg__panel'}, BuilderSettings ? h(BuilderSettings,{config:config.builder,saving:builderBusy,onDirty:value=>{setBuilderDirty(value);if(value)setBuilderMessage('');},registerSave:registerBuilderSave.current}) : panelError==='header_footer' ? loadError() : loadingPanel),
      h('section', { id: 'rdct-panel-identity', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-identity', hidden: active !== 'identity', className: 'rdct-stg__panel' },
        h('h3', null, labels.identity),
        h('div', { className: 'rdct-stg__identity' },
          config.siteLogo && h('img', { src: config.siteLogo, alt: config.siteTitle }),
          h('strong', null, config.siteTitle),
        ),
        h('p', null, labels.identityHint),
        h('a', { className: 'rdct-btn', href: config.identityUrl }, labels.customizeIdentity),
        h(ToggleField, { name: 'svg_uploads', label: labels.svgUploads, description: labels.svgUploadsHint, value: values.svg_uploads, onChange: change }),
      ),
      h('section', { id: 'rdct-panel-analytics', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-analytics', hidden: active !== 'analytics', className: 'rdct-stg__panel' },
        h('h3', null, labels.analytics), h(AnalyticsFields, { values, change, labels })),
      typographyView && h('section', { id: `rdct-panel-${active}`, role: 'tabpanel', 'aria-labelledby': `rdct-tab-${active}`, className: 'rdct-stg__panel' },
        h('h3', null, labels[active]),
        active === 'typography_loading' && h(ToggleField, { name: 'block_google_fonts', label: labels.blockGoogleFonts, description: labels.blockGoogleFontsHint, value: values.block_google_fonts, onChange: change }),
        TypographyFields ? h(TypographyFields, { config, values, change, group: fontGroup, mode: active === 'typography_loading' ? 'loading' : 'fonts' }) : panelError === 'typography' ? loadError() : loadingPanel,
      ),
      h('section', { id: 'rdct-panel-motion', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-motion', hidden: active !== 'motion', className: 'rdct-stg__panel' },
        h('p', { className: 'rdct-stg__breadcrumb' }, labels.effects),
        h('h3', null, labels.motion),
        h(ToggleField, { name: 'scroll_reveal', label: labels.scrollReveal, description: labels.scrollRevealHint, value: values.scroll_reveal, onChange: change }),
        h(ToggleField, { name: 'smooth_scroll', label: labels.smoothScroll, description: labels.smoothScrollHint, value: values.smooth_scroll, onChange: change }),
        values.smooth_scroll && h(InputField, { name: 'smooth_duration', label: labels.smoothDuration, type: 'number', min: .1, max: 3, step: .1, numeric: true, value: values.smooth_duration, onChange: change }),
        h('h4', null, labels.scrollbar),
        h(ToggleField, { name: 'custom_scrollbar', label: labels.customScrollbar, value: values.custom_scrollbar, onChange: change }),
        h('div', { hidden: !values.custom_scrollbar },
          h(InputField, { name: 'scrollbar_width', label: labels.scrollbarWidth, type: 'number', min: 4, max: 20, step: 1, numeric: true, value: values.scrollbar_width, onChange: change }),
          color('scrollbar_thumb', labels.scrollbarThumb),
          color('scrollbar_track', labels.scrollbarTrack),
          h('p', { className: 'rdct-stg__hint' }, labels.scrollbarHint),
        ),
      ),
      h('section', { id: 'rdct-panel-cursor', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-cursor', hidden: active !== 'cursor', className: 'rdct-stg__panel' },
        h('p', { className: 'rdct-stg__breadcrumb' }, labels.effects),
        h('h3', null, labels.cursor),
        h(CursorFields, { values, change, labels, defaults: config.defaults }),
      ),
      h('section', { id: 'rdct-panel-progress', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-progress', hidden: active !== 'progress', className: 'rdct-stg__panel' },
        h('p', { className: 'rdct-stg__breadcrumb' }, labels.effects),
        h('h3', null, labels.progress),
        h(ToggleField, { name: 'scroll_progress', label: labels.scrollProgress, description: labels.scrollProgressHint, value: values.scroll_progress, onChange: change }),
        h('div', { hidden: !values.scroll_progress },
          h(InputField, { name: 'scroll_progress_height', label: labels.progressHeight, type: 'number', min: 1, max: 8, step: 1, dir: 'ltr', value: values.scroll_progress_height, onChange: change }),
          ...[['scroll_progress_color', labels.progressColor], ['scroll_progress_track', labels.progressTrack]].map(([name, label]) => color(name, label)),
        ),
      ),
      h('section', { id: 'rdct-panel-preloader', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-preloader', hidden: active !== 'preloader', className: 'rdct-stg__panel' },
        h('p', { className: 'rdct-stg__breadcrumb' }, labels.effects),
        h('h3', null, labels.preloader),
        h(ToggleField, { name: 'preloader', label: labels.preloaderEnabled, description: labels.preloaderHint, value: values.preloader, onChange: change }),
        h('div', { hidden: !values.preloader },
          h(SelectField, { name: 'preloader_style', label: labels.preloaderStyle, description: labels.preloaderSafety, value: values.preloader_style, onChange: change, options: [['curtain', labels.curtain], ['fade', labels.fade], ['sliding', labels.sliding], ['spinner', labels.spinner], ['spinner-classic', labels.spinnerClassic], ['dissolve', labels.dissolve], ['image', labels.preloaderImage]] }),
          color('preloader_background', labels.preloaderBackground),
          values.preloader_style === 'curtain' && h(ColorSetting, { name: 'preloader_background_secondary', label: labels.preloaderBackgroundSecondary, value: values.preloader_background_secondary, fallback: values.preloader_color, resetLabel: labels.inheritAppearance, onChange: change }),
          ['spinner', 'spinner-classic'].includes(values.preloader_style) && color('preloader_color', labels.preloaderColor),
        ),
      ),
      h('section', { id: 'rdct-panel-design', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-design', hidden: active !== 'design', className: 'rdct-stg__panel' },
        h('h3', null, labels.design),
        ColorSettingsFields ? h(ColorSettingsFields, { config, values, change }) : panelError === 'design' ? loadError() : loadingPanel,
      ),
      h('section', { id: 'rdct-panel-layout', role: 'tabpanel', 'aria-labelledby': 'rdct-tab-layout', hidden: active !== 'layout', className: 'rdct-stg__panel' },
        h('h3', null, labels.layout), h('p', null, labels.layoutOwnershipHint),
        h(InputField, { name: 'container_width', label: labels.containerWidth, description: labels.widthHint, type: 'number', dir: 'ltr', min: 960, max: 1600, step: 1, value: values.container_width, onChange: change }),
        h(InputField, { name: 'reading_width', label: labels.readingWidth, type: 'number', dir: 'ltr', min: 320, max: 1280, step: 1, value: values.reading_width, onChange: change }),
        h(ContentLayoutFields, { values, change, config })),
      contentTypographyView && h('section', {id:`rdct-panel-${active}`,role:'tabpanel','aria-labelledby':`rdct-tab-${active}`,className:'rdct-stg__panel'},
        h('h3',null,`${labels[active.split('_')[0]]} / ${labels.typography}`),
        ContentTypographyFields ? h(ContentTypographyFields,{key:active,scope:active.split('_')[0],config,values,change}) : panelError === 'content_typography' ? loadError() : loadingPanel),
      active === 'page_title' && h('section',{id:'rdct-panel-page_title',role:'tabpanel','aria-labelledby':'rdct-tab-page_title',className:'rdct-stg__panel'},PageTitleSettings ? h(PageTitleSettings,{config,values,change}) : panelError === 'page_title' ? loadError() : loadingPanel),
      contentView && h('section', { id: `rdct-panel-${active}`, role: 'tabpanel', 'aria-labelledby': `rdct-tab-${active}`, className: 'rdct-stg__panel' },
        h('h3', null, `${labels[active.split('_')[0]]} — ${active.endsWith('_archive') ? labels.archiveView : labels.singleView}`),
        ContentViewFields ? h(ContentViewFields, { key: active, scope: active, config, values, change }) : panelError === 'content' ? loadError() : loadingPanel),
      config.canEditCode && ['custom_css', 'custom_js'].map((key) => h('section', { key, id: `rdct-panel-${key}`, role: 'tabpanel', 'aria-labelledby': `rdct-tab-${key}`, hidden: active !== key, className: 'rdct-stg__panel' },
        active === key && (CodeFields ? h(CodeFields, { kind: key === 'custom_css' ? 'css' : 'js', values, change, config }) : !codeError && loadingPanel), codeError && h('p', { role: 'alert' }, labels.codeEditorUnavailable))),
    ),
  );
}
