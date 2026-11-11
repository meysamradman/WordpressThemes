export function resolveProfile(base, raw, roles) {
  const values = {...base};
  const overrides = Object.fromEntries(Object.entries(raw).filter(([, value]) => value !== ''));
  for (const group of ['persian', 'latin']) {
    if (overrides[`${group}_font`]) {
      for (const role of roles) values[`${role}_${group}_font`] = '';
    } else if (overrides[`headings_${group}_font`]) {
      for (let level = 1; level <= 6; level++) values[`h${level}_${group}_font`] = '';
    }
  }
  return {...values, ...overrides};
}

