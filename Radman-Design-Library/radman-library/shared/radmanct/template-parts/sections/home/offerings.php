<?php
defined( 'ABSPATH' ) || exit;
$rdct_offerings = \RadmanCT\Home\offerings();
if(!$rdct_offerings)return;
?>
<section id="services" class="rdc-clt-next rdc-svcs" aria-label="<?php esc_attr_e('What we build','radmanct'); ?>">
 <div class="rdc-ss" data-rdc-svcs data-mode="services" data-transition="slide-scale" data-min-width="1024" data-min-scale=".9" data-offset="20">
 <?php foreach($rdct_offerings as $index=>$item): ?><div class="rdc-ss__itm"><?php get_template_part('shared/radmanct/template-parts/sections/home/offering-panel',null,array('index'=>$index,'item'=>$item,'component_inner'=>true)); ?></div><?php endforeach; ?>
 </div>
</section>
