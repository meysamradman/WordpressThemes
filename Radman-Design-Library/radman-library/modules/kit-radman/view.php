<?php defined('ABSPATH')||exit; ?>
<section class="rdl-kit-map rdc-ctr rdc-main"><h1>مجموعهٔ شرکتی سفارشی Radman</h1><p>صفحه‌ها و اجزای این مجموعه از یک نسخهٔ مشترک استفاده می‌کنند. نمونهٔ موردنیاز را باز کنید.</p><ul>
<?php foreach(['page-home','page-services','portfolio-archive','portfolio-single','header-radman','footer-radman'] as $slug):$item=\RadmanLibrary\modules()[$slug]; ?><li><a href="<?php echo esc_url(\RadmanLibrary\Samples\link($slug)); ?>"><?php echo esc_html($item['title']); ?> ←</a></li><?php endforeach; ?>
</ul></section>