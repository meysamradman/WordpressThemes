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
    if(!isset($manifest[$key])&&str_starts_with($key,'src/'))$key='shared/radmanct/'.$key;
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
