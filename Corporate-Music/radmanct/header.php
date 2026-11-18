<?php defined('ABSPATH')||exit; ?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('rdc-site'); ?>><?php wp_body_open(); ?>
<div class="rdc-page-curtain" aria-hidden="true" inert>
 <?php for($panel=0;$panel<27;$panel++): ?>
  <span style="--rdc-panel:<?php echo (int)$panel; ?>"></span>
 <?php endfor; ?>
</div>
<?php
wp_print_inline_script_tag(<<<'JS'
(() => {
 const reload = performance.getEntriesByType('navigation')[0]?.type === 'reload';
 let ticket;
 try {
  const key = 'rdc:page-curtain';
  const stored = sessionStorage.getItem(key);
  sessionStorage.removeItem(key);
  ticket = JSON.parse(stored);
 } catch {}
 const destination = location.pathname + location.search;
 const internal = ticket?.destination === destination && ticket.expires > Date.now();
 const preference = matchMedia('(prefers-reduced-motion:reduce)');
 if ((!reload && !internal) || preference.matches) return;
 const root = document.documentElement;
 const panel = document.querySelector('.rdc-page-curtain > :last-child');
 if (!panel) return;
 const finish = () => {
  clearTimeout(safety);
  panel.removeEventListener('animationend', finish);
  preference.removeEventListener('change', finish);
  window.removeEventListener('pagehide', finish);
  if (root.getAttribute('data-rdc-page-load') !== 'enter') return;
  root.removeAttribute('data-rdc-page-load');
  document.dispatchEvent(new Event('rdc:curtain-end'));
 };
 const safety = setTimeout(finish, 650);
 panel.addEventListener('animationend', finish, {once:true});
 preference.addEventListener('change', finish, {once:true});
 window.addEventListener('pagehide', finish, {once:true});
 root.setAttribute('data-rdc-page-load', 'enter');
})();
JS, ['id'=>'rdc-curtain-entry','data-nowprocket'=>true]);
?>
<a class="rdc-skip" href="#main-content"><?php esc_html_e('Skip to content','radmanct'); ?></a>
<div id="rdc-scr-wrp"><div id="rdc-scr-cnt"><?php get_template_part('template-parts/layout/header'); ?>
