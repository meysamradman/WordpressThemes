<?php defined('ABSPATH')||exit;$data=apply_filters('radmanct/music-home/data',array()); ?>
<footer class="rdc-ftr rdc-ctr"><p class="rdc-ftr__social-title"><?php echo esc_html(get_bloginfo('name')); ?> را در <span>شبکه‌های اجتماعی</span> دنبال کنید</p><nav class="rdc-ftr__socials" aria-label="شبکه‌های اجتماعی">
<?php foreach($data['socials']??array() as $social): ?><span class="rdc-ftr__social" role="img" aria-label="<?php echo esc_attr($social['label'].'؛ نشانی به‌زودی اضافه می‌شود'); ?>"><?php echo \RadmanCT\icon($social['icon']); ?></span><?php endforeach; ?>
</nav><small class="rdc-ftr__note">لوگو، تصاویر و نشانی‌های رسمی پس از دریافت تکمیل می‌شوند.</small></footer>
