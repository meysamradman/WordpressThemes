<?php

namespace RadmanCT\Builder\Tree;

defined( 'ABSPATH' ) || exit;

function items( array $rows ): \Generator {
	foreach ( $rows as $row ) {
		foreach ( $row['items'] as $item ) {
			yield $item;
			if ( 'container' === $item['type'] ) { yield from items( array( $item ) ); }
		}
	}
}
