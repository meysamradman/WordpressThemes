<?php defined('ABSPATH')||exit; ?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('rdc-site'); ?>><?php wp_body_open(); ?>
<div class="rdc-page-curtain" aria-hidden="true" inert>
 <?php for($panel=0;$panel<12;$panel++): ?>
  <span style="--rdc-panel:<?php echo (int)$panel; ?>"></span>
 <?php endfor; ?>
</div>
<?php
wp_print_inline_script_tag(<<<'JS'
(() => {
 try {
  const key = 'rdc:page-curtain';
  const expires = Number(sessionStorage.getItem(key));
  sessionStorage.removeItem(key);
  if (!(expires > Date.now()) || matchMedia('(prefers-reduced-motion:reduce)').matches) return;
  const root = document.documentElement;
  root.setAttribute('data-rdc-page-load', 'enter');
  setTimeout(() => {
   if (root.getAttribute('data-rdc-page-load') === 'enter') root.removeAttribute('data-rdc-page-load');
  }, 310);
 } catch {}
})();
JS, ['id'=>'rdc-curtain-entry','data-nowprocket'=>true]);
?>
<a class="rdc-skip" href="#main-content"><?php esc_html_e('Skip to content','radmanct'); ?></a>
<div id="rdc-scr-wrp"><div id="rdc-scr-cnt"><?php get_template_part('template-parts/layout/header'); ?>
