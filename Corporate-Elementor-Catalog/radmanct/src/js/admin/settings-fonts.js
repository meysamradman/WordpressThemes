export function primaryFontGroup(config, values) {
  return values.font_language && values.font_language !== 'auto' ? values.font_language : config.primaryFontGroup;
}

export function fontGroups(config, values) {
  return values.mixed_language_fonts || config.multilingualFonts ? ['persian', 'latin'] : [primaryFontGroup(config, values)];
}
