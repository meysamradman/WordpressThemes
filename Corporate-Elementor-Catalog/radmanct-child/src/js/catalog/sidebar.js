import '../content/sidebar.js';

const archive = document.querySelector('[data-rdct-archive="catalog"]');
archive?.addEventListener('rdct:archive-updated', (event) => {
  const params = new URLSearchParams(event.detail?.filters || new URL(location.href).searchParams);
  for (const input of archive.querySelectorAll('[data-rdct-catalog-search] [name^="rm_"]')) {
    input.value = params.get(input.name) || '';
  }
  for (const link of archive.querySelectorAll('[data-rdct-category]')) {
    const url = new URL(link.href);
    for (const key of ['rm_search', 'rm_brand', 'rm_feature', 'rm_sort', 'rm_tag']) {
      if (params.get(key)) url.searchParams.set(key, params.get(key));
      else url.searchParams.delete(key);
    }
    link.href = url.href;
    const current = params.get('rm_category') === link.dataset.rdctCategory;
    if (current) {
      link.setAttribute('aria-current', 'page');
      let parent = link.parentElement;
      while (parent && parent !== archive) {
        if (parent.tagName === 'DETAILS') parent.open = true;
        parent = parent.parentElement;
      }
    } else link.removeAttribute('aria-current');
  }
  for (const link of archive.querySelectorAll('[data-rdct-tag]')) {
    const url = new URL(link.href);
    for (const key of ['rm_search', 'rm_category', 'rm_brand', 'rm_feature', 'rm_sort']) {
      if (params.get(key)) url.searchParams.set(key, params.get(key));
      else url.searchParams.delete(key);
    }
    link.href = url.href;
    if (params.get('rm_tag') === link.dataset.rdctTag) link.setAttribute('aria-current', 'page');
    else link.removeAttribute('aria-current');
  }
});
