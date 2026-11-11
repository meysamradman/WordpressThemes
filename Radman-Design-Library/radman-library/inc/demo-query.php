<?php
namespace RadmanLibrary\DemoQuery;
defined('ABSPATH')||exit;
function render(string $type,string $layout,bool $native,callable $renderer):void {
    $previous=$GLOBALS['wp_query'];$post=$GLOBALS['post']??null;$id=get_queried_object_id();
    if(!$native){
        $query=['post_type'=>$type,'post_status'=>'publish','has_password'=>false];
        if($layout==='single'){$ids=get_posts($query+['posts_per_page'=>1,'fields'=>'ids']);if(!$ids){echo '<p>هنوز محتوایی اضافه نشده است.</p>';return;}$query['p']=$ids[0];}
        else{$query['posts_per_page']=8;$query['paged']=max(1,is_scalar($_GET['rdl_pg']??1)?absint($_GET['rdl_pg']??1):1);}
        $GLOBALS['wp_query']=new \WP_Query($query);
    }
    $link=static fn($url,$page)=>add_query_arg('rdl_pg',(int)$page,get_permalink($id));
    if(!$native)add_filter('get_pagenum_link',$link,10,2);
    try{$renderer();}finally{
        if(!$native)remove_filter('get_pagenum_link',$link,10);
        $GLOBALS['wp_query']=$previous;$GLOBALS['post']=$post;if($post)setup_postdata($post);
    }
}
