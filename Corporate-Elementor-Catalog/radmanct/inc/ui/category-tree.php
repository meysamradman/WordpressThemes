<?php

namespace RadmanCT\UI;

defined( 'ABSPATH' ) || exit;

function category_tree( array $terms, array $options = array() ): void {
	if ( function_exists( '\\RadmanCTComponents\\UI\\category_tree' ) ) { \RadmanCTComponents\UI\category_tree( $terms, $options ); }
}
