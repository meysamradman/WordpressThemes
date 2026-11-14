<?php
if(PHP_SAPI!=='cli')exit(1);require '/var/www/html/wp-load.php';
if(home_url()!=='http://127.0.0.1:8098')exit(1);
$user=get_user_by('login',getenv('MUSIC_ADMIN_USER'));if(!$user||!wp_check_password(getenv('MUSIC_ADMIN_PASSWORD'),$user->user_pass,$user->ID)||!user_can($user,'manage_options'))throw new RuntimeException('Administrator verification failed');
$urls=['home'=>home_url('/'),'music'=>home_url('/music/'),'blog'=>home_url('/blog/'),'about'=>home_url('/about-us/'),'contact'=>home_url('/contact-us/')];
foreach(['track','post'] as $type){$items=get_posts(['post_type'=>$type,'posts_per_page'=>1,'post_status'=>'publish']);if(!$items)throw new RuntimeException('Missing demo '.$type);$urls[$type]=get_permalink($items[0]);if($type==='track'){$audio=RadmanCT\Music\file((int)RadmanCT\Music\meta($items[0]->ID)['audio']);if(empty($audio['url']))throw new RuntimeException('Missing audio');$urls['audio']=$audio['url'];}}
echo wp_json_encode(['theme'=>get_stylesheet(),'wp'=>get_bloginfo('version'),'admin_verified'=>true,'posts'=>(int)wp_count_posts('post')->publish,'tracks'=>(int)wp_count_posts('track')->publish,'urls'=>$urls],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
