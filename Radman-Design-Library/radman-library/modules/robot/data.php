<?php
namespace RadmanLibrary\Designs\Robot;
defined('ABSPATH')||exit;
function data():array {
    $uri=get_template_directory_uri().'/modules/robot/';
    return apply_filters('rdl_robot_data',[
        'title'=>'کمی تعامل، یک تجربهٔ متفاوت.',
        'description'=>'نشانگر را حرکت دهید؛ نگاه ربات همراه شماست.',
        'model'=>$uri.'models/robot.glb','poster'=>$uri.'poster.svg',
        'config'=>['pivot'=>'Head_Pivot','camera'=>[0,.35,6.5],'scale'=>1,'rotation'=>[0,0,0],
            'yaw'=>20,'pitch'=>12,'follow'=>9,'return'=>6,'dpr'=>1.5,'transparent'=>true,'interaction'=>true,'mobileFallback'=>true],
    ]);
}
