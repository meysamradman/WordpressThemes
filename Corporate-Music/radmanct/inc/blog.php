<?php
namespace RadmanCT\Blog;
defined('ABSPATH')||exit;
require_once __DIR__.'/ui/category-tree.php';
function categories(int $id):string {
 $links=array();foreach(get_the_category($id)as$term)$links[]='<a href="'.esc_url(get_category_link($term)).'">'.esc_html($term->name).'</a>';return implode('، ',$links);
}
