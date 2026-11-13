<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
require_once __DIR__.'/table.php';
$data=$args['data']??\RadmanLibrary\Designs\DataTable\data();
$tag=($args['heading_tag']??'h2')==='h1'?'h1':'h2';
$heading=wp_unique_id('rdl-tbl-ttl-');
?>
<section class="rdl-tbl" aria-labelledby="<?php echo esc_attr($heading); ?>">
 <div class="rdl-tbl__inr">
  <header class="rdl-tbl__hdr">
   <p class="rdl-tbl__lbl"><?php echo esc_html($data['label']); ?></p>
   <<?php echo $tag; ?> class="rdl-tbl__ttl" id="<?php echo esc_attr($heading); ?>"><?php echo esc_html($data['title']); ?></<?php echo $tag; ?>>
   <p class="rdl-tbl__desc"><?php echo esc_html($data['description']); ?></p>
  </header>
  <?php \RadmanLibrary\Designs\DataTable\render($data); ?>
 </div>
</section>
