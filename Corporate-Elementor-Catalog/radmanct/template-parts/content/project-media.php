<?php
defined( 'ABSPATH' ) || exit;
require_once get_template_directory() . '/inc/integrations/content-media.php';
\RadmanCT\Content\Media\render( get_the_ID() );
