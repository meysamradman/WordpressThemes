<?php
defined('ABSPATH')||exit;
require_once __DIR__.'/data.php';
$items=$args['items']??\RadmanLibrary\Designs\TextImages\data();
$tag=in_array($args['heading_tag']??'h2',array('h1','h2','h3'),true)?($args['heading_tag']??'h2'):'h2';
$heading=wp_unique_id('rdl-tx-im-ttl-');
?>
<section class="rdl-tx-im" data-rdl-tx-im aria-labelledby="<?php echo esc_attr($heading); ?>">
 <<?php echo $tag; ?> class="rdl-tx-im__ttl" id="<?php echo esc_attr($heading); ?>">
 <?php foreach($items as $item): if(trim($item['text']??'')==='')continue; ?>
  <span class="rdl-tx-im__part"><?php echo esc_html($item['text']); ?><?php
   $id=(int)($item['image']??0);
   if($id&&\RadmanCT\PageTitle\attachment_allowed($id)&&wp_attachment_is_image($id)){
    echo ' <span class="rdl-tx-im__md" aria-hidden="true">'.wp_get_attachment_image($id,'medium',false,array('alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width:600px) 64px, 140px')).'</span>';
   }
  ?></span>
 <?php endforeach; ?>
 </<?php echo $tag; ?>>
</section>
