<?php defined('ABSPATH')||exit;$data=\RadmanCT\Footer\data();$missing=false; ?>
<footer class="rdc-ftr rdc-ctr"><p class="rdc-ftr__social-title"><?php echo esc_html(get_bloginfo('name')); ?> را در <span>شبکه‌های اجتماعی</span> دنبال کنید</p><nav class="rdc-ftr__socials" aria-label="شبکه‌های اجتماعی">
<?php foreach($data['socials']??array() as $social):$url=esc_url($social['url']??'');$tag=$url?'a':'span';if(!$url)$missing=true; ?>
 <<?php echo $tag; ?> class="rdc-ftr__social"<?php if($url): ?> href="<?php echo $url; ?>"<?php else: ?> role="img"<?php endif; ?> aria-label="<?php echo esc_attr($social['title'].($url?'':'؛ نشانی هنوز اضافه نشده')); ?>"><?php echo \RadmanCT\icon($social['icon']); ?></<?php echo $tag; ?>>
<?php endforeach; ?></nav><?php if($missing): ?><small class="rdc-ftr__note">نشانی شبکه‌های اجتماعی پس از دریافت فعال می‌شوند.</small><?php endif; ?></footer>
