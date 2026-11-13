<?php
namespace RadmanLibrary\Designs\DataTable;
defined('ABSPATH')||exit;

/** Native table contract adapted from Radman CT Components; no editor dependency. */
function render(array $data):void {
 $columns=is_array($data['columns']??null)?array_slice(array_values($data['columns']),0,12):array();
 $rows=is_array($data['rows']??null)?array_values(array_filter(array_slice($data['rows'],0,100),static fn($row)=>is_array($row)&&is_array($row['cells']??null))):array();
 if(!$columns||!$rows)return;
 $text=static fn($value):string=>is_scalar($value)?(string)$value:'';
 $caption=wp_unique_id('rdl-tbl-cap-');$hint=wp_unique_id('rdl-tbl-hint-');
 ?>
 <div class="rdl-tbl__card">
  <div class="rdl-tbl__meta"><span>اطلاعات نمونه</span><span><?php echo esc_html(number_format_i18n(count($rows))); ?> ویژگی <span aria-hidden="true">·</span> مدل <bdi><?php echo esc_html($text($data['model']??'')); ?></bdi></span></div>
  <div class="rdl-tbl__win" role="region" tabindex="0" aria-labelledby="<?php echo esc_attr($caption); ?>" aria-describedby="<?php echo esc_attr($hint); ?>">
   <table class="rdl-tbl__table">
    <caption id="<?php echo esc_attr($caption); ?>"><?php echo esc_html($text($data['caption']??'جدول اطلاعات')); ?></caption>
    <thead><tr><?php foreach($columns as $column): ?><th scope="col"><?php echo esc_html($text($column)); ?></th><?php endforeach; ?></tr></thead>
    <tbody>
     <?php foreach($rows as $row): ?><tr>
      <?php foreach($columns as $index=>$column): $cell=$text($row['cells'][$index]??''); ?>
       <?php if($index===0): ?><th scope="row"><?php echo esc_html($cell); ?></th>
       <?php else: ?><td<?php if($index===2)echo ' class="rdl-tbl__value"'; ?>><bdi><?php echo esc_html($cell); ?></bdi></td><?php endif; ?>
      <?php endforeach; ?>
     </tr><?php endforeach; ?>
    </tbody>
   </table>
  </div>
  <p class="rdl-tbl__hint" id="<?php echo esc_attr($hint); ?>">برای دیدن ستون‌های بیشتر در نمایشگر کوچک، جدول را افقی جابه‌جا کنید.</p>
 </div>
 <?php
}
