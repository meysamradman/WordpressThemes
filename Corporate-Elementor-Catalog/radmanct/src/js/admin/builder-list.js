import { createBuilderRequest } from './builder-api.js';

const config = window.rdctBuilderList;
const request = createBuilderRequest(config);
let busy = false;

function notice(root, message, error = false) {
  const target = root.querySelector('[data-rdct-list-notice]');
  target.className = `notice ${error ? 'notice-error' : 'notice-success'}`;
  const text = document.createElement('p');
  text.textContent = message;
  target.replaceChildren(text);
}

document.addEventListener('click', async (event) => {
  const button = event.target.closest('[data-rdct-delete]');
  const root = button?.closest('#rdct-layout-library');
  if (!root || busy) return;
  const title = button.closest('tr').querySelector('.row-title').textContent;
  if (!window.confirm(`${config.labels.confirmDelete}\n${title}`)) return;
  busy = true;
  button.disabled = true;
  root.setAttribute('aria-busy', 'true');
  let deleted = false;
  try {
    await request('delete', button.dataset.rdctDelete);
    deleted = true;
    const response = await fetch(window.location.href, { credentials: 'same-origin', cache: 'no-store' });
    if (!response.ok || response.redirected) throw new Error(config.labels.requestError);
    const page = new DOMParser().parseFromString(await response.text(), 'text/html');
    const updated = page.querySelector('#rdct-layout-library');
    if (!updated) throw new Error(config.labels.requestError);
    root.replaceWith(updated);
    notice(updated, config.labels.deleted);
    updated.querySelector('h1').focus();
  } catch (error) {
    if (deleted) {
      window.location.reload();
      return;
    }
    notice(root, error.message || config.labels.requestError, true);
    button.focus();
  } finally {
    busy = false;
    button.disabled = false;
    root.removeAttribute('aria-busy');
  }
});
