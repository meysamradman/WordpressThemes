<?php defined('ABSPATH')||exit;$data=\RadmanCT\Footer\data(); ?>
<footer class="rdc-ftr rdc-ctr"><p class="rdc-ftr__social-title"><?php echo esc_html(get_bloginfo('name')); ?> را در <span>شبکه‌های اجتماعی</span> دنبال کنید</p><?php get_template_part('template-parts/ui/socials',null,['items'=>$data['socials']??[]]); ?></footer>
