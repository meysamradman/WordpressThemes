<?php
define( 'ABSPATH', '/' );
$singular='post'; $archive=''; $taxonomy=''; $home=false; $protected=false; $matched=true; $calls=0;
function is_singular($types=null){return null===$types?(bool)$GLOBALS['singular']:in_array($GLOBALS['singular'],(array)$types,true);}
function is_post_type_archive($types){return in_array($GLOBALS['archive'],(array)$types,true);}
function is_tax($types=null){return null===$types?(bool)$GLOBALS['taxonomy']:in_array($GLOBALS['taxonomy'],(array)$types,true);}
function is_archive(){return (bool)($GLOBALS['archive']||$GLOBALS['taxonomy']);}
function is_home(){return $GLOBALS['home'];}
function is_search(){return false;}
function is_category(){return false;}
function is_tag(){return false;}
function is_author(){return false;}
function is_date(){return false;}
function post_password_required(){return $GLOBALS['protected'];}
function elementor_location_exits($location,$match){++$GLOBALS['calls'];return $GLOBALS['matched'];}
function elementor_theme_do_location($location){echo $location;return true;}
function expect($condition,$message){if(!$condition)throw new RuntimeException($message);}
require (getenv('RADMAN_THEME_ROOT')?:dirname(__DIR__).'/radmanct').'/inc/renderers.php';
foreach(['post','page','radman_product','radman_portfolio'] as $type){
 $singular=$type;$archive='';
 expect(\RadmanCT\Renderers\selected('single'),'Singular content must select Elementor.');
 ob_start();$rendered=\RadmanCT\Renderers\render();$html=ob_get_clean();
 expect($rendered&&$html==='single','Matching single template must render.');
 expect(!\RadmanCT\Renderers\selected('header')&&!\RadmanCT\Renderers\selected('footer'),'Content routing must not take over the Radman shell.');
}
$singular='';
foreach(['radman_product','radman_portfolio','other'] as $type){$archive=$type;expect(\RadmanCT\Renderers\selected('archive'),'Archives must select Elementor.');}
$archive='';$home=true;expect(\RadmanCT\Renderers\uses_elementor(),'Blog index must support Theme Builder.');
$home=false;$singular='page';$protected=true;$calls=0;
expect(!\RadmanCT\Renderers\render()&&$calls===0,'Protected content must never enter Theme Builder.');
$protected=false;$matched=false;expect(!\RadmanCT\Renderers\render(),'Missing conditions must permit native content fallback.');
echo "Elementor content routing, shell ownership and password boundary passed.\n";
