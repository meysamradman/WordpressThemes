<?php
namespace RadmanLibrary\Catalog;
defined('ABSPATH')||exit;
require_once __DIR__.'/catalog/model.php';
require_once __DIR__.'/catalog/query.php';
require_once __DIR__.'/catalog/admin.php';
require_once get_template_directory().'/shared/radmanct/inc/ui/category-tree.php';
const TAX=['radman_product_cat'=>'دسته‌های محصول','radman_product_brand'=>'برندها','radman_product_feature'=>'ویژگی‌های محصول','radman_product_tag'=>'برچسب‌های محصول'];
add_action('init',static function():void{
 register_post_type('radman_product',['labels'=>['name'=>'محصولات کاتالوگ','singular_name'=>'محصول','add_new_item'=>'افزودن محصول','edit_item'=>'ویرایش محصول'],'public'=>true,'show_in_rest'=>true,'has_archive'=>'products','rewrite'=>['slug'=>'products','with_front'=>false],'menu_icon'=>'dashicons-products','supports'=>['title','editor','excerpt','thumbnail','revisions','custom-fields']]);
 foreach(TAX as $tax=>$label)register_taxonomy($tax,'radman_product',['label'=>$label,'public'=>true,'show_in_rest'=>true,'show_admin_column'=>true,'hierarchical'=>$tax==='radman_product_cat','rewrite'=>['slug'=>['radman_product_cat'=>'product-category','radman_product_brand'=>'brand','radman_product_feature'=>'product-feature','radman_product_tag'=>'product-tag'][$tax],'with_front'=>false]]);
 Model\register('radman_product');
});
function native(?\WP_Query $q=null):bool {$q??=$GLOBALS['wp_query'];return $q->is_singular('radman_product')||$q->is_post_type_archive('radman_product')||$q->is_tax(array_keys(TAX))||($q->is_search()&&$q->get('post_type')==='radman_product');}
function url():string {return get_post_type_archive_link('radman_product')?:home_url('/products/');}
function render(string $layout):void {$view=static function()use($layout){require get_template_directory().'/modules/catalog-'.$layout.'/template.php';};if(native())$view();else \RadmanLibrary\DemoQuery\render('radman_product',$layout,false,$view);}
function image(int $id):bool {
 if(!wp_attachment_is_image($id)||!in_array(get_post_status($id),['inherit','publish'],true))return false;
 $parent=wp_get_post_parent_id($id);return !$parent||(is_post_publicly_viewable($parent)&&!post_password_required($parent));
}
function card(\WP_Post $item,string $heading='h2'):void {
 $heading=$heading==='h3'?'h3':'h2';echo '<article class="rdl-prd-card"><a href="'.esc_url(get_permalink($item)).'">';
 if(!post_password_required($item)&&image(get_post_thumbnail_id($item)))echo get_the_post_thumbnail($item,'medium_large',['loading'=>'lazy','sizes'=>'(max-width:600px) 100vw,33vw']);
 echo '<div><'.$heading.'>'.esc_html(get_the_title($item)).'</'.$heading.'>';if(!post_password_required($item)){$data=Model\read($item->ID);if($data['code'])echo '<p>'.esc_html($data['code']).'</p>';if($data['pricing']['mode']==='price'&&$data['pricing']['text'])echo '<p>'.esc_html($data['pricing']['text']).'</p>';}
 echo '<span>مشاهدهٔ محصول ←</span></div></a></article>';
}
