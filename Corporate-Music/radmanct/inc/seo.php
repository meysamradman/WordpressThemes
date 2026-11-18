<?php
namespace RadmanCT\SEO;
defined('ABSPATH')||exit;

function enabled():bool {
    $plugin=defined('WPSEO_VERSION')||defined('RANK_MATH_VERSION')||defined('AIOSEO_VERSION')||defined('SEOPRESS_VERSION')||defined('THE_SEO_FRAMEWORK_VERSION')||defined('SLIM_SEO_VERSION');
    return (bool)apply_filters('radmanct/seo/enabled',!$plugin);
}
function plain(string $value):string {
    $value=wp_strip_all_tags(strip_shortcodes($value));
    $value=html_entity_decode($value,ENT_QUOTES,get_bloginfo('charset')?:'UTF-8');
    return trim(preg_replace('/\s+/u',' ',$value)??'');
}
function description():string {
    $text='';
    if(is_search()||is_404()||is_preview())return '';
    if(is_singular()){
        $post=get_queried_object();
        if(!$post instanceof \WP_Post||'publish'!==$post->post_status||''!==$post->post_password)return '';
        $text=$post->post_excerpt?:preg_replace('/<!--.*?-->/s',' ',$post->post_content);
    }elseif(is_home()){
        $page=get_post((int)get_option('page_for_posts'));
        $text=$page&&'publish'===$page->post_status&&''===$page->post_password?($page->post_excerpt?:$page->post_content):'';
        if(''===plain((string)$text))$text=sprintf(__('Articles, tutorials and insights from %s.','radmanct'),get_bloginfo('name'));
    }elseif(is_category()||is_tag()||is_tax()){
        $term=get_queried_object();
        $text=$term instanceof \WP_Term?$term->description:'';
        if(''===plain((string)$text)&&$term instanceof \WP_Term)$text=sprintf(__('Browse %1$s on %2$s.','radmanct'),$term->name,get_bloginfo('name'));
    }elseif(is_post_type_archive(array('track','album','clip'))){
        $type=get_post_type_object(get_query_var('post_type'));
        if($type)$text=sprintf(__('Browse %1$s on %2$s.','radmanct'),$type->labels->name,get_bloginfo('name'));
    }
    if(is_front_page()&&''===plain((string)$text)){
        $tagline=get_bloginfo('description');
        if($tagline!==__('Just another WordPress site'))$text=$tagline;
    }
    $text=plain((string)apply_filters('radmanct/seo/description',(string)$text));
    if(''===$text)return '';
    return wp_html_excerpt($text,180,'…');
}
function archive_canonical():string {
    $url='';
    if(is_home())$url=get_option('page_for_posts')?get_permalink((int)get_option('page_for_posts')):home_url('/');
    elseif(is_post_type_archive(array('track','album','clip'))){
        $type=get_query_var('post_type');
        if(is_string($type))$url=get_post_type_archive_link($type);
    }
    elseif(is_category()||is_tag()||is_tax())$url=get_term_link(get_queried_object());
    elseif(is_author())$url=get_author_posts_url(get_queried_object_id());
    elseif(is_day())$url=get_day_link((int)get_query_var('year'),(int)get_query_var('monthnum'),(int)get_query_var('day'));
    elseif(is_month())$url=get_month_link((int)get_query_var('year'),(int)get_query_var('monthnum'));
    elseif(is_year())$url=get_year_link((int)get_query_var('year'));
    if(is_wp_error($url)||!$url)return '';
    $page=max(1,(int)get_query_var('paged'));
    if($page>1){
        global $wp_rewrite;
        $url=$wp_rewrite->using_permalinks()?user_trailingslashit(trailingslashit($url).$wp_rewrite->pagination_base.'/'.$page,'paged'):add_query_arg('paged',$page,$url);
    }
    return $url;
}
add_action('wp_head',static function():void{
    if(!enabled()||is_feed()||is_404()||is_search()||is_preview())return;
    $description=description();
    if(''!==$description)echo '<meta name="description" content="'.esc_attr($description).'">'."\n";
    if(!is_singular()){
        $url=archive_canonical();
        if($url)echo '<link rel="canonical" href="'.esc_url($url).'">'."\n";
    }
},5);
add_filter('wp_robots',static function(array $robots):array{
    if(!enabled())return $robots;
    $private=is_singular()&&('publish'!==get_post_status(get_queried_object_id())||''!==(get_queried_object()->post_password??''));
    if(is_search()||is_404()||is_preview()||$private){$robots['noindex']=true;unset($robots['index']);}
    return $robots;
});

function clip_schema():array {
    if(!is_singular('clip')||is_preview())return [];
    $post=get_queried_object();
    if(!$post instanceof \WP_Post||$post->post_status!=='publish'||$post->post_password!=='')return [];
    $clip=\RadmanCT\Videos\item($post);
    $image=$clip['image'];
    if(!\RadmanCT\PageTitle\attachment_allowed($image)||!wp_attachment_is_image($image))return [];
    $poster=wp_get_attachment_image_url($image,'large');
    $description=plain($clip['desc']);
    $title=plain($clip['title']);
    if(!$poster||$description===''||$title==='')return [];

    $source=[];
    $date=get_post_datetime($post,'date','gmt');
    if($clip['aparat']){
        // The channel import stores Aparat's publication timestamp as the post date.
        // An embed entered manually has no verified original publication date.
        if(!get_post_meta($post->ID,'_rdc_aparat_poster',true))return [];
        $source=['embedUrl'=>\RadmanCT\Videos\embed_url($clip['aparat'])];
    }elseif($file=\RadmanCT\Videos\file($clip['video'])){
        $uploaded=get_post_datetime($file['id'],'date','gmt');
        if($uploaded&&$date&&$uploaded>$date)$date=$uploaded;
        $source=['contentUrl'=>$file['url']];
    }
    if(!$source||!$date)return [];
    return [
        '@type'=>'VideoObject',
        '@id'=>$clip['url'].'#video',
        'name'=>$title,
        'description'=>$description,
        'thumbnailUrl'=>$poster,
        'uploadDate'=>$date->format(DATE_W3C),
    ]+$source;
}

add_filter('wpseo_schema_graph',static function(array $graph):array {
    if(!is_singular('clip'))return $graph;
    foreach($graph as $node)if(in_array('VideoObject',(array)($node['@type']??[]),true))return $graph;
    $video=clip_schema();
    if(!$video)return $graph;
    $video_id=$video['@id'];
    foreach($graph as &$node){
        if(!in_array('WebPage',(array)($node['@type']??[]),true))continue;
        $links=$node['video']??[];
        if(isset($links['@id']))$links=[$links];
        $node['video']=array_merge((array)$links,[['@id'=>$video_id]]);
        break;
    }
    unset($node);
    $graph[]=$video;
    return $graph;
},20);

add_action('wp_head',static function():void {
    if(defined('WPSEO_VERSION')||!enabled())return;
    $video=clip_schema();
    if(!$video)return;
    echo '<script type="application/ld+json">'.wp_json_encode(['@context'=>'https://schema.org']+$video,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>'."\n";
},6);
