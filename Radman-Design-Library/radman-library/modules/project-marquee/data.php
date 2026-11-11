<?php
namespace RadmanLibrary\Designs\Marquee;
defined('ABSPATH')||exit;
function data():array {
    return apply_filters('rdl/marquee/data',[
        'title'=>['دربارهٔ پروژه‌ات','گفت‌وگو کنیم'],
        'url'=>home_url('/'),
        'images'=>get_option('rdl_reference_media',[]),
    ]);
}
