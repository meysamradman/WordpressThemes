<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
require_once get_template_directory().'/inc/ui/carousel.php';
$data=\RadmanLibrary\Designs\CardCarousel\data();
$heading=wp_unique_id('rdl-cards-ttl-');
?>
<section class="rdl-cards" data-rdl-cards-layout="<?php echo esc_attr($data['layout']); ?>" aria-labelledby="<?php echo esc_attr($heading); ?>" dir="<?php echo esc_attr($data['direction']); ?>">
 <header class="rdl-cards__hdr"><div><h2 id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></h2><p><?php echo esc_html($data['desc']); ?></p></div><?php echo \RadmanCT\button('شروع یک پروژه',$data['link']); ?></header>
 <?php \RadmanLibrary\UI\Carousel\render($data['cards'],static function(array $card):void{require __DIR__.'/card.php';},array('label'=>$data['title'],'direction'=>$data['direction'],'layout'=>$data['layout'])); ?>
</section>
