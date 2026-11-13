<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
require_once get_template_directory().'/shared/radmanct/inc/icons.php';
require_once get_template_directory().'/shared/radmanct/inc/ui/button.php';
$data=$args['data']??\RadmanLibrary\Designs\ButtonRoll\data();
$tag=($args['heading_tag']??'h2')==='h1'?'h1':'h2';
$heading=wp_unique_id('rdl-btn-demo-ttl-');
?>
<section class="rdl-btn-demo" aria-labelledby="<?php echo esc_attr($heading); ?>">
 <div class="rdl-btn-demo__inr">
  <header class="rdl-btn-demo__hdr">
   <p class="rdl-btn-demo__lbl">دکمهٔ متن چرخشی</p>
   <<?php echo $tag; ?> class="rdl-btn-demo__ttl" id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></<?php echo $tag; ?>>
   <p class="rdl-btn-demo__desc"><?php echo esc_html($data['description']); ?></p>
  </header>
  <div class="rdl-btn-demo__grid">
   <?php foreach(array(false=>'زمینهٔ روشن',true=>'زمینهٔ تیره') as $inverse=>$label): ?>
    <div class="rdl-btn-demo__stage<?php if($inverse)echo ' rdc-inv'; ?>">
     <p class="rdl-btn-demo__label"><?php echo esc_html($label); ?></p>
     <div class="rdl-btn-demo__button"><?php echo \RadmanCT\button($data['label'],$data['url'],array('icon'=>$data['icon'])); ?></div>
    </div>
   <?php endforeach; ?>
  </div>
  <p class="rdl-btn-demo__hint">نشانگر را روی دکمه ببرید یا با کلید Tab روی آن قرار بگیرید.</p>
 </div>
</section>
