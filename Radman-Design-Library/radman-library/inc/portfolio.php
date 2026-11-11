<?php
namespace RadmanLibrary\Portfolio;
defined('ABSPATH')||exit;
foreach(['content/projects','content/media','content/continuation','media'] as $part)require_once get_template_directory().'/shared/radmanct/inc/'.$part.'.php';
function native():bool {return is_post_type_archive('project')||is_tax('project_category')||is_singular('project');}
function render(string $layout):void {
    $previous_query=$GLOBALS['wp_query'];$previous_post=$GLOBALS['post']??null;
    $design_id=get_queried_object_id();
    if(!native()){
        $query=['post_type'=>'project','post_status'=>'publish','has_password'=>false];
        if($layout==='single'){
            $ids=get_posts($query+['posts_per_page'=>1,'fields'=>'ids']);
            if(!$ids){echo '<p>ابتدا یک نمونه‌کار از مدیریت اضافه کنید.</p>';return;}
            $query['p']=$ids[0];
        }else{$query['posts_per_page']=6;$query['paged']=max(1,is_scalar($_GET['rdl_pg']??1)?absint($_GET['rdl_pg']??1):1);}
        $GLOBALS['wp_query']=new \WP_Query($query);
    }
    $page_link=static fn($url,$page)=>add_query_arg('rdl_pg',(int)$page,get_permalink($design_id));
    // Native conditions change after swapping the query; retain the original route explicitly.
    $is_demo=$previous_query->is_singular('rdl_design');
    if($is_demo)add_filter('get_pagenum_link',$page_link,10,2);
    try{require get_template_directory().'/shared/radmanct/templates/portfolio-'.$layout.'.php';}
    finally{
        if($is_demo)remove_filter('get_pagenum_link',$page_link,10);
        $GLOBALS['wp_query']=$previous_query;$GLOBALS['post']=$previous_post;
        if($previous_post)setup_postdata($previous_post);
    }
}
