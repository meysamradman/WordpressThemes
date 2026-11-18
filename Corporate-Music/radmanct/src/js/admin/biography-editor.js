const panel = document.querySelector('[data-rdc-works]');

if (panel) {
 const body = panel.querySelector('tbody');
 const template = panel.querySelector('template');
 const add = panel.querySelector('[data-rdc-work-add]');

 add.addEventListener('click', () => {
  const row = template.content.firstElementChild.cloneNode(true);
  body.append(row);
  row.querySelector('input').focus();
 });

 body.addEventListener('click', event => {
  const remove = event.target.closest('[data-rdc-work-remove]');
  if (!remove) return;
  const row = remove.closest('tr');
  const next = row.nextElementSibling || row.previousElementSibling;
  row.remove();
  (next?.querySelector('[data-rdc-work-remove]') || add).focus();
 });
}
