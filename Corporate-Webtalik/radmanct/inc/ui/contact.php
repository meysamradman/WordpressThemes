<?php
namespace RadmanCT\Contact;
defined('ABSPATH')||exit;

function data():array {
 static $data;
 if($data===null)$data=apply_filters('radmanct/contact/data',array('label'=>__('Contact us','radmanct'),'channels'=>array()));
 return $data;
}

function visible():bool {
 return !is_admin()&&!wp_doing_ajax()&&!is_feed()&&!is_embed()&&!is_preview()&&!is_404()&&!is_search()&&!empty(data()['channels']);
}
