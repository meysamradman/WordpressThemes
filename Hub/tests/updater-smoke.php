<?php
// Offline checks for update discovery, cache/backoff and preservation of other offers.
define('ABSPATH', __DIR__);
define('MINUTE_IN_SECONDS',60); define('HOUR_IN_SECONDS',3600); define('DAY_IN_SECONDS',86400);
$cache=[]; $ttl=[]; $hooks=[]; $calls=0; $events=[]; $checks=0; $allowed=true;
$reply=['new_version'=>'6.0.4','url'=>'https://hub.liquid-themes.com/changelog/','package'=>'https://api.liquid-themes.com/updates/hub.zip'];
function sanitize_key($s){return $s;}
function add_action($hook,$callback){$GLOBALS['hooks'][$hook][]=$callback;}
function add_filter($hook,$callback){add_action($hook,$callback);}
function get_transient($k){return $GLOBALS['cache'][$k]??false;}
function set_transient($k,$v,$time){$GLOBALS['cache'][$k]=$v;$GLOBALS['ttl'][$k]=$time;}
function wp_next_scheduled($k){return isset($GLOBALS['events'][$k]);}
function wp_schedule_event($time,$interval,$hook){$GLOBALS['events'][$hook]=[$time,$interval];}
function add_query_arg($args,$url){return $url.'?'.http_build_query($args);}
function wp_remote_get($url,$args){$GLOBALS['calls']++;$GLOBALS['last_args']=$args;parse_str(parse_url($url,PHP_URL_QUERY),$GLOBALS['query']);return $GLOBALS['reply'];}
function is_wp_error($r){return $r===false;}
function wp_remote_retrieve_response_code($r){return 200;}
function wp_remote_retrieve_body($r){return json_encode($r);}
function esc_url_raw($s){return $s;}
function current_user_can($cap){return $GLOBALS['allowed'];}
function wp_verify_nonce($n,$action){return $n==='ok'&&$action==='upgrade-core';}
function sanitize_text_field($s){return $s;}
function wp_unslash($s){return $s;}
function check($yes,$why){if(!$yes){throw new Exception($why);} $GLOBALS['checks']++;}
require dirname(__DIR__).'/hub/liquid/admin/updater/liquid-updater-class.php';
$updater=new Liquid_Updater();
check($calls===0,'Bootstrap must not request updates');
$initial=(object)['response'=>['other'=>['new_version'=>'2.0']]];
$updater->theme_update_transient($initial);
check($calls===0,'Reading uncached badge must not request updates');
$updater->schedule();$updater->schedule();
check(count($events)===1 && $events[Liquid_Updater::CRON_HOOK][1]==='twicedaily','Only one periodic event');
$updater->refresh();
check($calls===1 && $query['license']==='' && $query['action']==='get_version','Vendor API without fabricated key');
check($last_args['timeout']===5,'Bound request duration');
check($ttl['hub-update-last-attempt']===12*HOUR_IN_SECONDS,'Successful checks cached for 12 hours');
$offer=$updater->theme_update_transient($initial);
check($offer->response['hub']['new_version']==='6.0.4' && isset($offer->response['other']),'Expose update and preserve other offers');
$updater->refresh(); $updater->theme_update_transient($offer);
check($calls===1,'Cached admin/front reads must not repeat requests');
$reply=false; $updater->refresh(true);
check($ttl['hub-update-last-attempt']===30*MINUTE_IN_SECONDS,'Back off failed checks');
check($updater->theme_update_transient($offer)->response['hub']['new_version']==='6.0.4','Failure retains last good update');
$reply=['new_version'=>['bad']];$updater->refresh(true);
check($cache['hub-update-response']['new_version']==='6.0.4','Malformed response cannot erase valid update');
$reply=['new_version'=>'6.0.3'];$updater->refresh(true);
$offer=$updater->theme_update_transient($offer);
check(!isset($offer->response['hub']) && isset($offer->no_update['hub']),'Current upstream version is not a false update');
$allowed=false;$before=$calls;$updater->updates_screen();
check($calls===$before,'No manual request for unauthorized user');
$allowed=true;$_GET=['force-check'=>1,'_wpnonce'=>'bad'];$updater->updates_screen();
check($calls===$before,'Invalid force-check nonce cannot bypass cache');
$_GET['_wpnonce']='ok';$updater->updates_screen();
check($calls===$before+1,'Native authorized Check again can refresh');
echo "PASS: $checks updater checks\n";
