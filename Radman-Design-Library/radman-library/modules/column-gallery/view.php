<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php'; ?>
<section class="rdl-cgal" data-rdl-cgal aria-label="گالری ستون‌های خلاف‌جهت"><h1 class="screen-reader-text">گالری ستون‌های خلاف‌جهت</h1>
<div class="rdl-cgal__win"><div class="rdl-cgal__grid">
<?php foreach(\RadmanLibrary\Designs\ColumnGallery\data() as $column): ?><div class="rdl-cgal__col" data-rdl-cgal-col data-rdl-cgal-spd="<?php echo esc_attr((string)$column['speed']); ?>">
<?php foreach($column['images'] as $index=>$id):$id=(int)$id;if(!\RadmanCT\PageTitle\attachment_allowed($id)||!wp_attachment_is_image($id))continue; ?><figure class="rdl-cgal__itm"><?php echo wp_get_attachment_image($id,'large',false,['loading'=>$index<3?'eager':'lazy','decoding'=>'async','sizes'=>'33vw']); ?></figure><?php endforeach; ?>
</div><?php endforeach; ?></div></div></section>
