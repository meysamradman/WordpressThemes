<?php
namespace RadmanLibrary;
defined('ABSPATH')||exit;
function modules():array {
    static $items;
    return $items??=['starter'=>['title'=>'نمونهٔ پایه','slug'=>'starter']]+json_decode(file_get_contents(__DIR__.'/samples.json'),true,512,JSON_THROW_ON_ERROR);
}
function module(int $id):?array {
    return modules()[(string)get_post_meta($id,'_rdl_module',true)]??null;
}
