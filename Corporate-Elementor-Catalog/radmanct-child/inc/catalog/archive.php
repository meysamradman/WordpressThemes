<?php

namespace RadmanCTChild\Catalog\Archive;

defined( 'ABSPATH' ) || exit;

function values(): array {
	return \RadmanCT\Archives\filter_values( 'catalog' );
}
