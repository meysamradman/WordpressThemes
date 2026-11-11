<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/integrations/product-display.php';
echo \RadmanCT\Content\ProductDisplay\html( get_the_ID() );
