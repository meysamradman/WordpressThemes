<?php defined('ABSPATH')||exit;
$type=$args['type']??'album';if(!in_array($type,['album','clip'],true))return;
$taxonomy=$type.'_category';$terms=get_terms(['taxonomy'=>$taxonomy,'hide_empty'=>true,'pad_counts'=>true]);
require_once get_template_directory().'/inc/ui/category-tree.php'; ?>
<aside class="rdc-collection-cats" data-rdc-categories aria-label="دسته‌بندی"><h2>دسته‌بندی</h2><a class="rdc-collection-cats__all" href="<?php echo esc_url(get_post_type_archive_link($type)); ?>"<?php if(is_post_type_archive($type))echo ' aria-current="page"'; ?>>همهٔ موارد</a>
<?php if(!is_wp_error($terms))\RadmanCT\UI\category_tree($terms,['current'=>is_tax($taxonomy)?get_queried_object_id():0,'ancestors'=>is_tax($taxonomy)?get_ancestors(get_queried_object_id(),$taxonomy):[]]); ?></aside>
