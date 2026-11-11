export {observeSidebarOffset} from '../content/sidebar.js';

const archive = document.querySelector('[data-rdct-archive="blog"]');
archive?.addEventListener('rdct:archive-updated', (event) => {
  for (const input of archive.querySelectorAll('form[data-rdct-archive-form] input[name="s"]')) {
    input.value = event.detail?.filters?.rdct_search || '';
  }
});
