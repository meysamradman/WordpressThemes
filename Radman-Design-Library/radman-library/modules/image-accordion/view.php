<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php'; ?>
<section class="rdl-iac" data-rdl-iac aria-label="آکاردئون تصویری"><h1 class="screen-reader-text">آکاردئون تصویری</h1><div class="rdl-iac__row">
<?php foreach(\RadmanLibrary\Designs\ImageAccordion\data() as $index=>$item):$id=(int)($item['image']??0);if(!\RadmanCT\PageTitle\attachment_allowed($id)||!wp_attachment_is_image($id))continue; ?>
<article class="rdl-iac__itm" data-rdl-iac-itm><div class="rdl-iac__md"><?php echo wp_get_attachment_image($id,'full',false,['loading'=>'eager','decoding'=>'async','sizes'=>'(max-width:767px) 100vw, 60vw']); ?></div>
<button class="rdl-iac__tgl" type="button" data-rdl-iac-tgl hidden aria-label="<?php echo esc_attr('بازکردن تصویر: '.$item['title']); ?>"></button></article>
<?php endforeach; ?></div></section>
