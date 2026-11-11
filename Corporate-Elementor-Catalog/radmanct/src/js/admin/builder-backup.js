const formats = new Set(['rdct-builder', 'radman-builder']);

export function downloadBackup(layout, kind, origin, environment = globalThis) {
  const blob = new environment.Blob([JSON.stringify({ format: 'rdct-builder', version: 1, kind, origin, layout })], { type: 'application/json' });
  if (blob.size > 262144) throw new Error('Backup exceeds the file size limit');
  const url = environment.URL.createObjectURL(blob);
  let anchor;
  try {
    anchor = environment.document.createElement('a');
    anchor.href = url; anchor.download = `rdct-${kind}.json`; anchor.hidden = true;
    environment.document.body.append(anchor); anchor.click();
  } catch (error) { environment.URL.revokeObjectURL(url); throw error; }
  finally { anchor?.remove(); }
  environment.setTimeout(() => environment.URL.revokeObjectURL(url), 1000);
}

export async function readBackup(file, kind, origin) {
  if (!file || !Number.isFinite(file.size) || file.size > 262144 || file.size < 0) return null;
  const contents = await file.text();
  if (contents.length > 262144) return null;
  let backup;
  try { backup = JSON.parse(contents); } catch { return null; }
  if (!backup || typeof backup !== 'object' || Array.isArray(backup) || !formats.has(backup.format)
    || backup.version !== 1 || backup.kind !== kind || backup.origin !== origin) return null;
  return backup.layout;
}

export function createBackupImporter(request, kind, origin) {
  let pending;
  const cancel = (notify = false) => {
    const session = pending; pending = null;
    if (session) { session.controller.abort(); if (notify) session.status('cancelled'); }
  };
  const load = async (file, id, { isCurrent, confirm, apply, report, errorMessage, status = () => {} }) => {
    cancel();
    const controller = new AbortController(); const session = { controller, status }; pending = session;
    const active = () => pending === session && !controller.signal.aborted && isCurrent();
    try {
      status('reading');
      const layout = await readBackup(file, kind, origin);
      if (!active()) return;
      if (!layout) throw new Error(errorMessage);
      status('validating');
      const result = await request('validate', id, { layout }, controller.signal);
      if (!active()) return;
      if (!confirm()) { status('cancelled'); return; }
      if (active()) { apply(result.layout); status('applied'); }
    } catch (error) {
      if (active() && error?.name !== 'AbortError') { status('error'); report(error?.message || errorMessage); }
    } finally {
      if (pending === session) pending = null;
    }
  };
  return { load, cancel };
}
