<?php defined('ABSPATH')||exit;get_template_part('template-parts/layout/footer'); ?></div></div>
<?php get_template_part('template-parts/layout/menu-panel'); ?>
<?php if(is_singular(array('project','album'))&&!post_password_required())get_template_part('template-parts/ui/lightbox'); ?>
<div class="rdc-scr-prog" aria-hidden="true" hidden></div><div class="rdc-cur" aria-hidden="true" hidden><span></span><small class="rdc-cur__lbl"><span data-rdc-cur-lbl></span><?php echo \RadmanCT\icon('arrow-up-right'); ?></small></div>
<?php get_template_part('template-parts/ui/contact'); ?>
<?php wp_footer(); ?></body></html>
