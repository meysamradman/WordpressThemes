export function compileCustomCss(value, id) {
  if (value && typeof value === 'object') value = value.$$type === 'string' && !value.disabled ? value.value : '';
  if (typeof value !== 'string' || new TextEncoder().encode(value).length > 16384 || !/^[a-zA-Z0-9_-]{1,80}$/.test(id) || /\0|<\/\s*(?:style|script)\b/i.test(value)) return null;
  const css = value.replace(/^[ \t\r\n\v\f]+|[ \t\r\n\v\f]+$/g, '');
  if (!css) return null;
  let output = ''; const stack = [];
  for (let i = 0; i < css.length; i++) {
    const char = css[i];
    if (char === '/' && css[i + 1] === '*') {
      const end = css.indexOf('*/',i + 2);
      if (end === -1) return null;
      output += ' '; i = end + 1; continue;
    }
    if (char === '"' || char === "'") {
      const start = i;
      while (++i < css.length) { if (css[i] === '\\') { i++; continue; } if (css[i] === char) break; }
      if (i >= css.length) return null;
      output += css.slice(start,i + 1); continue;
    }
    if (char === '\\' || /^(?:(?:-webkit-)?image-set|src)\s*\(/i.test(css.slice(i))) return null;
    if (char === '@' && !/^@(media|supports|container|starting-style)\b/i.test(css.slice(i))) return null;
    if ('{(['.includes(char)) stack.push(char);
    if ('})]'.includes(char) && stack.pop() !== {'}':'{',')':'(',']':'['}[char]) return null;
    if (char === 's' && !stack.includes('[') && !/[a-zA-Z0-9_.#-]/.test(css[i - 1] ?? '') && /^selector(?![a-zA-Z0-9_-]|\s*\()/.test(css.slice(i)) && /^[^{};]*\{/.test(css.slice(i + 8))) { output += '&'; i += 7; continue; }
    output += char;
  }
  if (stack.length) return null;
  for (const match of output.matchAll(/url\(\s*(['"]?)(.*?)\1\s*\)/gis)) {
    if (!/^(?:\/(?!\/)|\.{1,2}\/|[a-zA-Z0-9_-])[a-zA-Z0-9_./?%=&#+~-]*$/.test(match[2].trim())) return null;
  }
  let hash = 2166136261;
  for (const byte of new TextEncoder().encode(css)) hash = Math.imul(hash ^ byte,16777619) >>> 0;
  const className = `rdct-css-${id}-${hash.toString(16).padStart(8,'0')}`;
  return {class:className, css:`.${className}{${output}\n}`};
}
