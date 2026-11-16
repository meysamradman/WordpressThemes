<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';require_once get_template_directory().'/inc/ui/carousel.php';
$data=\RadmanLibrary\Designs\VentureCarousel\data();$heading=wp_unique_id('rdl-venture-ttl-');
?>
<section class="rdl-venture" data-rdl-venture aria-labelledby="<?php echo esc_attr($heading); ?>">
 <header class="screen-reader-text"><p>پروژه‌های فردا</p><h2 id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></h2><p><?php echo esc_html($data['desc']); ?></p></header>
 <?php \RadmanLibrary\UI\Carousel\render($data['cards'],static function(array $card,int $index):void{$detail_id=wp_unique_id('rdl-venture-detail-');require __DIR__.'/card.php';},['direction'=>$data['direction'],'label'=>$data['title'],'layout'=>'bleed']); ?>
</section>
