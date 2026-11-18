<?php defined('ABSPATH')||exit; ?>
<?php $contact=is_page('contact-us')&&!post_password_required(get_queried_object_id()); ?>
<footer class="rdc-ftr rdc-ctr<?php if($contact)echo ' rdc-ftr--contact'; ?>">
 <p class="rdc-ftr__social-title"><?php echo $contact?'علی اسدی':esc_html(get_bloginfo('name')); ?> را در <span>شبکه‌های اجتماعی</span> دنبال کنید</p>
 <?php if($contact): ?><p class="rdc-ftr__handle" dir="ltr"><?php echo esc_html('@'.\RadmanCT\Client\social_handle()); ?></p><?php endif; ?>
 <?php get_template_part('template-parts/ui/socials',null,['items'=>\RadmanCT\Client\socials()]); ?>
</footer>
