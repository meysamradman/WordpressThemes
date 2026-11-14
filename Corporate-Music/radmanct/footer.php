<?php defined('ABSPATH')||exit;get_template_part('template-parts/layout/footer'); ?></div></div>
<?php get_template_part('template-parts/layout/menu-panel'); ?>
<?php if(is_singular('project')): ?><dialog class="rdc-lb rdc-inv" aria-label="<?php esc_attr_e('Image preview','radmanct'); ?>"><button type="button" class="rdc-menu-tgl rdc-dlg-cls" data-rdc-lb-cls aria-label="<?php esc_attr_e('Close','radmanct'); ?>"><?php echo \RadmanCT\icon('x'); ?></button><img alt=""></dialog><?php endif; ?>
<div class="rdc-scr-prog" aria-hidden="true" hidden></div><div class="rdc-cur" aria-hidden="true" hidden><span></span><small class="rdc-cur__lbl"><span data-rdc-cur-lbl></span><?php echo \RadmanCT\icon('arrow-up-right'); ?></small></div>
<?php get_template_part('template-parts/ui/contact'); ?>
<?php wp_footer(); ?></body></html>
