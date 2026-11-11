<?php
namespace RadmanCT\Preloader;
defined('ABSPATH')||exit;
add_action('wp_body_open',static function():void{
    $asset=\RadmanCT\Assets\entry('src/js/preloader.js');
    if(!$asset||!\RadmanCT\Assets\entry('src/css/site.css'))return;
    $script=file_get_contents(get_template_directory().'/dist/'.$asset['file']);
    if(!is_string($script)||''===trim($script))return;
    $entrance=is_front_page()&&!post_password_required()?' data-entrance="1"':'';
    echo '<div id="rdc-pre" data-exit-duration="912"'.$entrance.' aria-hidden="true" inert hidden>';
    for($i=0;$i<27;$i++)echo '<span class="rdc-pre__pnl" style="--rdc-pnl:'.$i.'"></span>';
    echo '</div>';
    wp_print_inline_script_tag($script,array('id'=>'rdc-pre-init','data-nowprocket'=>true));
},1);
