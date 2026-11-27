<?php
defined('ABSPATH')||exit;
if(($args['mode']??'numbered')!=='more'){
 the_posts_pagination();
 return;
}
global $wp_query;
$next=get_next_posts_page_link((int)$wp_query->max_num_pages);
if(!$next)return;
?>
<nav class="rdc-arc-pg" data-rdc-arc-pg aria-label="<?php esc_attr_e('Posts pagination','radmanct'); ?>">
 <?php echo \RadmanCT\button(__('Load more','radmanct'),$next,['data-rdc-load-more'=>true,'variant'=>'outline','icon'=>'plus']); ?>
</nav>
