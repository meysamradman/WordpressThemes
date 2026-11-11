<?php
$_SERVER['HTTP_HOST']='localhost:8096';
$_SERVER['REQUEST_METHOD']='GET';
require '/var/www/html/wp-load.php';
$calls=0;
$count=function($pre,$args,$url) use (&$calls) {
    if (strpos($url,'https://api.liquid-themes.com/hub')===0) {$calls++;}
    return $pre;
};
add_filter('pre_http_request',$count,1,3);
$updater=new Liquid_Updater();
for($i=0;$i<5;$i++){get_site_transient('update_themes');}
if($calls!==0){throw new Exception('Reading theme update state performed HTTP');}
$updater->refresh(true);
$live=get_transient('hub-update-response');
if(!is_array($live)||empty($live['new_version'])){throw new Exception('Live vendor response not available');}
if($calls!==1){throw new Exception('Expected one vendor metadata request');}
$updater->refresh();
get_site_transient('update_themes');
if($calls!==1){throw new Exception('Successful cache was not reused');}
try {
    $fixture=$live;$fixture['new_version']='6.0.4';
    set_transient('hub-update-response',$fixture,HOUR_IN_SECONDS);
    $native=get_site_transient('update_themes');
    if(!isset($native->response['hub'])||$native->response['hub']['new_version']!=='6.0.4'){
        throw new Exception('Native WordPress update offer not exposed');
    }
} finally {
    set_transient('hub-update-response',$live,7*DAY_IN_SECONDS);
    remove_filter('pre_http_request',$count,1);
}
if(!wp_next_scheduled(Liquid_Updater::CRON_HOOK)){throw new Exception('Periodic update event missing');}
echo 'PASS: live vendor '.$live['new_version'].'; native update fixture; 12-hour reuse; zero HTTP on badge reads; scheduled background check.'.PHP_EOL;
echo 'Theme local version: '.wp_get_theme('hub')->get('Version').PHP_EOL;
