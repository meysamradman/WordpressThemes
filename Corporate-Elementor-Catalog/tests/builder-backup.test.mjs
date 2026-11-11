import test from 'node:test';
import assert from 'node:assert/strict';
import { readBackup, createBackupImporter, downloadBackup } from '../radmanct/src/js/admin/builder-backup.js';

const origin = 'https://local.test';
const layout = { version: 1, enabled: false, rows: [] };
const file = (patch = {}, overrides = {}) => ({ size: 120, text: async () => JSON.stringify({ format: 'rdct-builder', version: 1, kind: 'header', origin, layout, ...patch }), ...overrides });
const deferred = () => { let resolve; let reject; const promise = new Promise((yes, no) => { resolve = yes; reject = no; }); return { promise, resolve, reject }; };
const flush = async () => { for (let i = 0; i < 6; ++i) await Promise.resolve(); };
function callbacks() {
  const applied = []; const errors = []; let valid = true; let accepted = true; let confirmations = 0;
  return { applied, errors, invalidate: () => { valid = false; }, decline: () => { accepted = false; }, confirmations: () => confirmations,
    options: { isCurrent: () => valid, confirm: () => { ++confirmations; return accepted; }, apply: (value) => applied.push(value), report: (value) => errors.push(value), errorMessage: 'Invalid backup' } };
}

test('backup reader accepts both product names only for the same site and builder', async () => {
  for (const format of ['rdct-builder', 'radman-builder']) assert.deepEqual(await readBackup(file({ format }), 'header', origin), layout);
  for (const patch of [{ format: 'other' }, { kind: 'footer' }, { version: 2 }, { origin: 'https://other.test' }]) assert.equal(await readBackup(file(patch), 'header', origin), null);
  assert.equal(await readBackup(file({}, { size: 262145, text: () => { throw new Error('Must not read oversized file'); } }), 'header', origin), null);
  for (const text of ['null', '[]', '{', 'x'.repeat(262145)]) assert.equal(await readBackup(file({}, { text: async () => text }), 'header', origin), null);
});

test('restoring validates with the server and confirmation changes only editor state', async () => {
  const view = callbacks(); const calls = [];
  const canonical = { ...layout, enabled: true };
  const importer = createBackupImporter(async (...args) => { calls.push(args); return { layout: canonical }; }, 'header', origin);
  await importer.load(file({ format: 'radman-builder' }), 'default', view.options);
  assert.equal(calls[0][0], 'validate'); assert.equal(calls[0][1], 'default'); assert.deepEqual(calls[0][2], { layout });
  assert.ok(calls[0][3] instanceof AbortSignal); assert.deepEqual(view.applied, [canonical]);
  view.decline(); await importer.load(file(), 'default', view.options); assert.equal(view.applied.length, 1);
  await importer.load(file({ kind: 'footer' }), 'default', view.options); assert.deepEqual(view.errors, ['Invalid backup']);
});

test('new restore, layout changes and unmount discard late file reads or validation responses', async () => {
  for (const reason of ['new', 'changed', 'cancel']) {
    const first = deferred(); const view = callbacks(); const signals = [];
    const importer = createBackupImporter((...args) => { signals.push(args[3]); return signals.length === 1 ? first.promise : Promise.resolve({ layout }); }, 'header', origin);
    const pending = importer.load(file(), 'default', view.options); await flush();
    if (reason === 'new') await importer.load(file(), 'default', view.options);
    else if (reason === 'changed') view.invalidate();
    else importer.cancel();
    first.resolve({ layout: { stale: true } }); await pending;
    assert.equal(view.applied.length, reason === 'new' ? 1 : 0); assert.equal(view.errors.length, 0);
    if (reason !== 'changed') assert.equal(signals[0].aborted, true);
  }
  const reading = deferred(); const view = callbacks(); let requests = 0;
  const importer = createBackupImporter(async () => { ++requests; }, 'header', origin);
  const pending = importer.load(file({}, { text: () => reading.promise }), 'default', view.options);
  importer.cancel(); reading.resolve(await file().text()); await pending;
  assert.equal(requests, 0); assert.equal(view.confirmations(), 0);
});

test('a validation failure preserves the editor and a later retry can succeed', async () => {
  const view = callbacks(); let fail = true;
  const importer = createBackupImporter(async () => { if (fail) throw new Error('Session expired'); return { layout }; }, 'header', origin);
  await importer.load(file(), 'default', view.options);
  assert.deepEqual(view.errors, ['Session expired']); assert.equal(view.applied.length, 0);
  fail = false; await importer.load(file(), 'default', view.options); assert.deepEqual(view.applied, [layout]);
});

test('restore phases stop on explicit cancel and stale requests cannot replace the active status', async () => {
  const first = deferred(); const phases = []; const view = callbacks(); let count = 0;
  const importer = createBackupImporter(async () => ++count === 1 ? first.promise : { layout }, 'header', origin);
  const pending = importer.load(file(), 'default', { ...view.options, status: (phase) => phases.push(phase) }); await flush();
  assert.deepEqual(phases, ['reading', 'validating']);
  importer.cancel(true); assert.equal(phases.at(-1), 'cancelled');
  await importer.load(file(), 'default', { ...view.options, status: (phase) => phases.push(phase) });
  assert.equal(phases.at(-1), 'applied'); const length = phases.length;
  first.resolve({ layout: { stale: true } }); await pending;
  assert.equal(phases.length, length); assert.deepEqual(view.applied, [layout]);
  importer.cancel(true); assert.equal(phases.length, length);
});

test('exports round-trip current drafts and release temporary DOM and URLs on success or failure', async () => {
  for (const fail of [false, true]) {
    let blob; let removed = 0; let revoked = 0; let appended = 0; let delayed;
    const anchor = { click() { if (fail) throw new Error('Download denied'); }, remove() { ++removed; } };
    const environment = { Blob, URL: { createObjectURL: (value) => { blob = value; return 'blob:local'; }, revokeObjectURL: () => ++revoked },
      document: { createElement: () => anchor, body: { append: () => ++appended } }, setTimeout: (fn) => { delayed = fn; } };
    if (fail) assert.throws(() => downloadBackup(layout, 'header', origin, environment), /Download denied/);
    else {
      downloadBackup(layout, 'header', origin, environment);
      assert.deepEqual(await readBackup({ size: blob.size, text: () => blob.text() }, 'header', origin), layout);
      assert.equal(anchor.download, 'rdct-header.json'); assert.equal(revoked, 0); delayed();
    }
    assert.equal(appended, 1); assert.equal(removed, 1); assert.equal(revoked, 1);
  }
  assert.throws(() => downloadBackup({ text: 'x'.repeat(262144) }, 'header', origin), /size limit/);
});
