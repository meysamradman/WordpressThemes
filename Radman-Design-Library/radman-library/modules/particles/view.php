<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$data=$args['data']??\RadmanLibrary\Designs\Particles\data();
$heading=wp_unique_id('rdl-ptc-ttl-');$tag=($args['heading_tag']??'h2')==='h1'?'h1':'h2';
?>
<section class="rdl-ptc rdc-inv" data-rdl-ptc data-rdl-ptc-options="<?php echo esc_attr(wp_json_encode($data['options'])); ?>" aria-labelledby="<?php echo esc_attr($heading); ?>">
 <div class="rdl-ptc__fallback" aria-hidden="true"></div>
 <div class="rdl-ptc__canvas" data-rdl-ptc-canvas aria-hidden="true"></div>
 <div class="rdl-ptc__copy">
  <p class="rdl-ptc__lbl"><?php echo esc_html($data['label']); ?></p>
  <<?php echo $tag; ?> class="rdl-ptc__ttl" id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></<?php echo $tag; ?>>
  <p class="rdl-ptc__desc"><?php echo esc_html($data['description']); ?></p>
 </div>
 <button class="rdl-ptc__pause" type="button" data-rdl-ptc-pause aria-pressed="false" hidden>توقف حرکت</button>
</section>
