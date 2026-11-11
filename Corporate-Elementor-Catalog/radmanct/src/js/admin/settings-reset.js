export function resetSettings(current, defaults, section, sectionKeys = [], canEditCode = false, primaryGroup = 'persian') {
  const keys = Object.keys(defaults).filter(key => {
    if (!canEditCode && (key.startsWith('custom_css') || key === 'custom_js')) return false;
    if (!section) return true;
    if (section === 'typography_loading') return ['block_google_fonts', 'font_language', 'mixed_language_fonts'].includes(key);
    if (['typography_primary', 'typography_secondary'].includes(section)) {
      const group = section === 'typography_primary' ? primaryGroup : primaryGroup === 'persian' ? 'latin' : 'persian';
      return key.includes(`${group}_`) || /^(body|headings|h[1-6]|link|span)(?:_hover)?_color$/.test(key);
    }
    if (section === 'typography') {
      return /^(body|headings|h[1-6]|link|span)(?:_hover)?_color$/.test(key) || key.endsWith('_font') || key.endsWith('_weight') || key.endsWith('_line_height')
        || /^(body|headings|h[1-6]|link|span)_(persian|latin)(_(tablet|mobile))?_size$/.test(key)
        || ['block_google_fonts', 'font_language', 'mixed_language_fonts'].includes(key);
    }
    if (section === 'layout') return key === 'container_width' || key === 'reading_width' || key.startsWith('content_');
    if (section === 'design') return key === 'brand_color';
    if (/^(blog|catalog|portfolio)_(archive|single)$/.test(section)) return key.startsWith(`${section}_`);
    return sectionKeys.includes(key);
  });
  return { ...current, ...Object.fromEntries(keys.map(key => [key, defaults[key]])) };
}
