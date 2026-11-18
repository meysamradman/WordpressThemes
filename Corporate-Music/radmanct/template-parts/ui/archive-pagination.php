<?php defined('ABSPATH')||exit;
$rtl=is_rtl();
$options=['prev_text'=>\RadmanCT\icon($rtl?'chevron-right':'chevron-left').'<span class="screen-reader-text">صفحهٔ قبلی</span>','next_text'=>\RadmanCT\icon($rtl?'chevron-left':'chevron-right').'<span class="screen-reader-text">صفحهٔ بعدی</span>','mid_size'=>1,'end_size'=>1];
if(isset($args['total'])){
 if((int)$args['total']<2)return;
 $links=paginate_links($options+['total'=>(int)$args['total'],'current'=>(int)$args['current'],'base'=>$args['base'],'type'=>'plain']);
 if($links)echo '<nav class="navigation pagination" aria-label="صفحه‌بندی تصاویر"><div class="nav-links">'.$links.'</div></nav>';
}else the_posts_pagination($options);
