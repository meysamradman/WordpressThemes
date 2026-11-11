<?php defined('ABSPATH')||exit; ?><div class="rdl-kit"><?php get_template_part('shared/radmanct/template-parts/layout/header'); ?>
<?php
require get_template_directory().'/modules/home-hero/view.php';
require get_template_directory().'/modules/home-services/view.php';
require get_template_directory().'/modules/home-projects/view.php';
get_template_part('shared/radmanct/template-parts/layout/footer'); get_template_part('shared/radmanct/template-parts/layout/menu-panel'); ?>
</div>