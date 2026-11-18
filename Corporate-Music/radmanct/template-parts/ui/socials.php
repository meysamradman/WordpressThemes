<?php defined('ABSPATH')||exit;$items=$args['items']??[];if(!$items)return; ?>
<nav class="rdc-socials" aria-label="شبکه‌های اجتماعی">
 <?php foreach($items as $social):$url=esc_url($social['url']??'');$tag=$url?'a':'span'; ?>
 <<?php echo $tag; ?> class="rdc-socials__itm"<?php if($url): ?> href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer"<?php else: ?> role="img"<?php endif; ?> aria-label="<?php echo esc_attr($social['title'].($url?' (تب جدید)':'')); ?>"><?php echo \RadmanCT\icon($social['icon']); ?></<?php echo $tag; ?>>
 <?php endforeach; ?>
</nav>
