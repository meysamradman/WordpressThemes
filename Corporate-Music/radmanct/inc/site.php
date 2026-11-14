<?php
namespace RadmanCT;
defined('ABSPATH')||exit;
function media_id(string $name):int { $map=get_option('webtalik_media',array());$id=(int)($map[$name]??0);return $id&&get_post_type($id)==='attachment'?$id:0; }
function page_heading():string {
 if(is_post_type_archive('project'))return __('Projects','radmanct');
 if(is_category()||is_tax()||is_tag())return single_term_title('',false);
 if(is_search())return sprintf(__('Search: %s','radmanct'),get_search_query());
 if(is_home())return __('Blog','radmanct');
 if(is_archive())return get_the_archive_title();
 return get_the_title();
}
