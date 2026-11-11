<?php
namespace RadmanLibrary\UI;
defined('ABSPATH')||exit;
function text_marquee(array $data):void {
 $words=array_values(array_filter($data['words']??[],static fn($word)=>is_string($word)&&trim($word)!==''));if(!$words)return;
 $id=absint($data['flower']??0); ?>
 <div class="rdl-mtx" data-rdl-mtx aria-label="نوار متن پیوسته"><div class="rdl-mtx__win"><div class="rdl-mtx__track"><div class="rdl-mtx__grp" dir="rtl"><?php foreach($words as $word): ?><span><?php echo esc_html($word); ?></span><?php if(\RadmanCT\PageTitle\attachment_allowed($id)&&wp_attachment_is_image($id))echo wp_get_attachment_image($id,'medium',false,['alt'=>'','width'=>128,'height'=>128,'loading'=>'eager']); ?><?php endforeach; ?></div></div></div><div class="rdl-mtx__tools"><button type="button" class="rdl-mtx__pause" data-rdl-mtx-pause hidden aria-pressed="false">توقف حرکت</button></div></div>
<?php }
