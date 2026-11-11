export function toRem(value) {
  if (value === '' || value == null || !Number.isFinite(Number(value))) return '';
  return String(Number((Number(value) / 16).toFixed(6)));
}

export function fromRem(value) {
  if (String(value).trim() === '' || !Number.isFinite(Number(value))) return '';
  return String(Math.round(Number(value) * 1600) / 100);
}
