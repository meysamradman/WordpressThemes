<?php
defined( 'ABSPATH' ) || exit;
if ( array( 'catalog', 'archive' ) === \RadmanCT\Renderers\context() ) {
	get_template_part( 'template-parts/content/archive', null, array( 'catalog' => true ) );
} else {
	get_template_part( 'index' );
}
