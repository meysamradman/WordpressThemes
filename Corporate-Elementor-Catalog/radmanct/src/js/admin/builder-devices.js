export const devices = [
  { id: 'desktop', width: 1440 }, { id: 'laptop', width: 1200 }, { id: 'tablet', width: 900 }, { id: 'mobile', width: 390 },
];
export const deviceAt = (width) => width < 768 ? 'mobile' : width < 1024 ? 'tablet' : width < 1440 ? 'laptop' : 'desktop';
export const deviceKey = (device, key) => device === 'desktop' ? key : `${device}${key[0].toUpperCase()}${key.slice(1)}`;
export function responsiveDefaults(row) {
  const values = { hiddenDevices: [] };
  for (const device of ['laptop', 'tablet', 'mobile']) {
    values[`${device}Align`] = 'inherit';
    if (row) {values[`${device}Justify`] = 'inherit'; values[`${device}Direction`] = 'inherit';}
    else values[`${device}Width`] = 'inherit';
    if (device === 'mobile') continue;
    if (row) {
      for (const field of ['Gap', 'Padding', 'Columns']) values[`${device}${field}`] = null;
      values[`${device}Layout`] = 'inherit';
    } else values[`${device}FontSize`] = 0;
  }
  return values;
}
export function hiddenDevices(value) {
  const result = new Set(value.hiddenDevices || []);
  if (value.visibility === 'desktop') result.add('mobile');
  if (value.visibility === 'mobile') ['desktop', 'laptop', 'tablet'].forEach((device) => result.add(device));
  return [...result];
}
