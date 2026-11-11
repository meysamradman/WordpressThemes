<?php defined('ABSPATH')||exit; ?><div class="rdl-kit"><?php get_template_part('shared/radmanct/template-parts/layout/header'); ?>
<?php
require get_template_directory().'/modules/services-cover/view.php';
require get_template_directory().'/modules/services-list/view.php';
require get_template_directory().'/modules/services-strip/view.php';
require get_template_directory().'/modules/services-faq/view.php';
get_template_part('shared/radmanct/template-parts/layout/footer'); get_template_part('shared/radmanct/template-parts/layout/menu-panel'); ?>
</div>