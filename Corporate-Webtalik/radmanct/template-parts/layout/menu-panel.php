<?php defined('ABSPATH')||exit;require_once get_template_directory().'/inc/navigation/panel-walker.php';$panel=\RadmanCT\Footer\data(); ?>
<dialog id="rdc-menu-dlg" class="rdc-menu-dlg rdc-inv" data-panel-motion="wave" aria-label="<?php esc_attr_e('Menu','radmanct'); ?>">
 <button type="button" data-rdc-menu-cls class="rdc-menu-tgl rdc-dlg-cls" aria-label="<?php esc_attr_e('Close menu','radmanct'); ?>"><?php echo \RadmanCT\icon('x'); ?></button>
 <div data-rdc-pnl-body>
 <div class="rdc-pnl__ly" data-rdc-pnl-mob>
  <nav class="rdc-nav" aria-label="<?php esc_attr_e('Primary menu','radmanct'); ?>"><?php wp_nav_menu(array('theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'menu_id'=>'rdc-pnl-nav','menu_class'=>'rdc-pnl-nav','depth'=>3,'walker'=>new \RadmanCT\Navigation\PanelWalker())); ?></nav>
  <section class="rdc-pnl__br" data-rdc-pnl-br hidden aria-labelledby="rdc-pnl-br-ttl">
   <div class="rdc-pnl__br-head">
    <button type="button" class="rdc-pnl-back" data-rdc-pnl-back><?php echo \RadmanCT\icon('chevron-down'); ?><span><?php esc_html_e('Back','radmanct'); ?></span></button>
    <p id="rdc-pnl-br-ttl" class="rdc-pnl__lbl" data-rdc-pnl-br-ttl></p>
   </div>
   <div data-rdc-pnl-br-cnt></div>
  </section>
  <?php if($panel['contact']): ?><aside class="rdc-pnl__info"><?php echo \RadmanCT\button(__('Start a conversation','radmanct'),$panel['contact'],array('data-rdc-pnl-rvl'=>true)); ?></aside><?php else: ?><aside class="rdc-pnl__info" hidden></aside><?php endif; ?>
 </div>
 <div data-rdc-pnl-desk-host hidden inert></div>
 </div>
 <template data-rdc-pnl-desk data-label="<?php esc_attr_e('Start a conversation','radmanct'); ?>">
  <?php get_template_part('template-parts/layout/menu-panel-desktop',null,array('panel'=>$panel)); ?>
 </template>
</dialog>
