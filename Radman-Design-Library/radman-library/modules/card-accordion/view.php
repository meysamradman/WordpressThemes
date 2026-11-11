<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php'; ?>
<section class="rdl-iac rdl-iac--card" data-rdl-iac aria-label="آکاردئون تصویر و باکس متن"><h1 class="screen-reader-text">آکاردئون تصویر و باکس متن</h1><div class="rdl-iac__row">
<?php foreach(\RadmanLibrary\Designs\CardAccordion\data() as $index=>$item):$id=(int)($item['image']??0);if(!\RadmanCT\PageTitle\attachment_allowed($id)||!wp_attachment_is_image($id))continue; ?>
<article class="rdl-iac__itm" data-rdl-iac-itm><div class="rdl-iac__md"><?php echo wp_get_attachment_image($id,'full',false,['loading'=>'eager','decoding'=>'async','sizes'=>'(max-width:767px) 100vw, 60vw']); ?></div>
<div class="rdl-iac__box"><p class="rdl-iac__lbl"><?php echo esc_html($item['label']); ?></p><h2><?php echo esc_html($item['title']); ?></h2><p class="rdl-iac__desc"><?php echo esc_html($item['desc']); ?></p></div>
<button class="rdl-iac__tgl" type="button" data-rdl-iac-tgl hidden aria-label="<?php echo esc_attr('بازکردن تصویر: '.$item['title']); ?>"></button></article>
<?php endforeach; ?></div></section>
