<?php
namespace RadmanCT\Service;
defined('ABSPATH')||exit;
function data():array {
 return apply_filters('radmanct/service/mosaic',array('label'=>'','title'=>'','items'=>array()));
}
function allowed(int $id):bool {
 if(!$id||get_post_type($id)!=='attachment'||!in_array(get_post_status($id),array('inherit','publish'),true)||post_password_required($id))return false;
 $parent=wp_get_post_parent_id($id);
 return !$parent||(is_post_publicly_viewable($parent)&&!post_password_required($parent));
}
