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
    }elseif(is_post_type_archive('project')){
        $text=__('Selected website projects, design and development case studies.','radmanct');
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
    elseif(is_post_type_archive('project'))$url=get_post_type_archive_link('project');
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
