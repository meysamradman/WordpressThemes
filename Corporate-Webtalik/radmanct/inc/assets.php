<?php
namespace RadmanCT\Assets;
defined( 'ABSPATH' ) || exit;
function entry( string $key ): array {
    static $manifest;
    if ( null === $manifest ) {
        $path = get_template_directory() . '/dist/manifest.json';
        $data = is_readable( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
        $manifest = is_array( $data ) ? $data : array();
    }
    $entry = $manifest[ $key ] ?? array();
    $file = $entry['file'] ?? '';
    return is_string( $file ) && preg_match( '~^(css|js)/[a-zA-Z0-9_-]+\.min\.(css|js)$~D', $file ) && is_file( get_template_directory() . '/dist/' . $file ) ? $entry : array();
}
function style( string $name, array $dependencies = array() ): void {
    $asset = entry( 'src/css/' . $name . '.css' );
    if ( $asset ) { wp_enqueue_style( 'rdc-' . str_replace( '/', '-', $name ), get_template_directory_uri() . '/dist/' . $asset['file'], $dependencies, null ); }
}
function url(string $file):string { return get_template_directory_uri().'/dist/'.$file; }
function module_dependencies(array $asset):array {
    static $registered=array();
    $dependencies=array();
    // Vite retains URL imports; registering the same URLs lets WordPress preload
    // only the static graph without making lazy animations eager.
    foreach(($asset['imports']??array()) as $key){
        $dependency=entry($key);
        if(!$dependency||!str_ends_with($dependency['file'],'.js'))continue;
        $id='rdc-chunk-'.substr(sha1($key),0,12);
        if(!isset($registered[$key])){
            $registered[$key]=true;
            wp_register_script_module($id,url($dependency['file']),module_dependencies($dependency),null);
        }
        $dependencies[]=array('id'=>$id,'import'=>'static');
    }
    return $dependencies;
}
function script(string $name):void { $asset=entry('src/js/'.$name.'.js');if($asset)wp_enqueue_script_module('rdc-'.str_replace('/','-',$name),url($asset['file']),module_dependencies($asset),null); }
add_action('wp_enqueue_scripts',static function():void{
 style('site');script('site');
 if(\RadmanCT\PageTitle\is_used())style('layout/page-title',array('rdc-site'));
 if(is_page('services')){style('pages/services',array('rdc-site'));script('pages/services');}
 elseif(is_front_page()){style('pages/home',array('rdc-site'));script('pages/home');}
 elseif(is_home()||is_category()||is_tag()||is_date()||is_author()||is_search()||is_singular('post')){style('pages/blog',array('rdc-site'));style(is_singular('post')?'pages/blog/single':'pages/blog/archive',array('rdc-pages-blog'));if(!is_singular())script('pages/archive');}
 elseif(is_post_type_archive('project')||is_tax('project_category')||is_singular('project')){style(is_singular('project')?'pages/portfolio/single':'pages/portfolio/archive',array('rdc-site'));if(is_singular('project')){if(!post_password_required())script('ui/gallery');}else{script('pages/archive');script('pages/portfolio');}}
});
add_filter( 'wp_preload_resources', static function ( array $resources ): array {
    if ( is_readable( get_template_directory() . '/fonts/PeydaWebVF.woff2' ) ) { $resources[] = array( 'href' => get_template_directory_uri() . '/fonts/PeydaWebVF.woff2', 'as' => 'font', 'type' => 'font/woff2', 'crossorigin' => 'anonymous' ); }
    return $resources;
} );

add_filter('wp_script_attributes',static function(array $attributes):array{if(($attributes['id']??'')==='rdc-site-js-module'&&defined('WP_DEBUG')&&WP_DEBUG)$attributes['data-rdc-debug']='1';return $attributes;});
