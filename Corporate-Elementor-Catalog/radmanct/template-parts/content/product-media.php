<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/integrations/product-media.php';
\RadmanCT\Content\ProductMedia\render( get_the_ID() );
