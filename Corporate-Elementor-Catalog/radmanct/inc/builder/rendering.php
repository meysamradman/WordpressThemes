<?php

namespace RadmanCT\Builder\Rendering;

defined( 'ABSPATH' ) || exit;

function item( string $kind, array $item, string $layout_id, bool $preview ): string {
	ob_start();
	try {
		if ( 'container' === $item['type'] ) {
			row( $kind, array( 'id' => $item['id'], 'appearance' => $item['layout'], 'items' => $item['items'] ), $layout_id, $preview );
		} elseif ( in_array( $item['type'], array( 'logo', 'image' ), true ) ) {
			require_once __DIR__ . '/logo.php';
			\RadmanCT\Builder\Logo\render( $item, $kind, 'logo' === $item['type'] );
		} elseif ( 'panel' === $item['type'] ) {
			require_once dirname( __DIR__ ) . '/navigation/panel.php';
			\RadmanCT\Navigation\Panel\render( $item, null, $kind . '-' . $item['id'] );
		} elseif ( 'theme-toggle' === $item['type'] ) {
			echo \RadmanCT\ColorScheme\toggle( $item['schemeOptions'] ?? array(), $preview );
		} elseif ( 'menu' === $item['type'] ) {
			\RadmanCT\Builder\menu( $kind, $item['id'], $item['menuId'] ?? 0, $item );
		} elseif ( 'block' === $item['type'] ) {
			echo \RadmanCT\Builder\content( ( $preview ? 'preview-' : '' ) . $kind . '-' . $layout_id, $item );
		} elseif ( 'text' === $item['type'] ) {
			$text = $preview ? $item['text'] : \RadmanCT\Builder\translate( $item['text'], $kind, $item['id'], 'text', $layout_id );
			if ( '' !== trim( $text ) ) { echo '<p class="rdct-bld__text">' . nl2br( esc_html( $text ) ) . '</p>'; }
		} elseif ( 'button' === $item['type'] ) {
			$url = $preview ? $item['url'] : \RadmanCT\Builder\translate( $item['url'], $kind, $item['id'], 'url', $layout_id );
			$text = $preview ? $item['text'] : \RadmanCT\Builder\translate( $item['text'], $kind, $item['id'], 'text', $layout_id );
			require_once __DIR__ . '/visuals.php';
			if ( ! $preview && isset( $item['buttonOptions']['secondary_label'] ) ) { $item['buttonOptions']['secondary_label'] = \RadmanCT\Builder\translate( $item['buttonOptions']['secondary_label'], $kind, $item['id'], 'button_secondary_label', $layout_id ); }
			if ( ! $preview && isset( $item['buttonOptions']['image_caption'] ) ) { $item['buttonOptions']['image_caption'] = \RadmanCT\Builder\translate( $item['buttonOptions']['image_caption'], $kind, $item['id'], 'button_image_caption', $layout_id ); }
			echo \RadmanCT\Builder\Visuals\button( $item, $text, $url );
		} elseif ( 'search' === $item['type'] ) {
			get_search_form();
		} elseif ( 'copyright' === $item['type'] ) {
			echo '<p class="rdct-bld__text">&copy; ' . esc_html( wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ) . '</p>';
		} elseif ( in_array( $item['type'], array( 'icon', 'contact', 'social' ), true ) ) {
			echo \RadmanCT\Builder\Elements\render( $kind, $item, $layout_id, $preview );
		}
		$html = (string) ob_get_contents();
		if ( $preview && '' === trim( $html ) ) {
			return '<span class="rdct-bld__placeholder" aria-hidden="true">' . esc_html__( 'Select to add content', 'radmanct' ) . '</span>';
		}
		return $html;
	} finally { ob_end_clean(); }
}

function row( string $kind, array $row, string $layout_id, bool $preview ): void {
	$markup = '';
	$appearances = array();
	foreach ( $row['items'] as $item ) {
		if ( in_array( $item['type'], array( 'icon', 'contact', 'social' ), true ) ) {
			require_once __DIR__ . '/elements.php';
			\RadmanCT\Builder\Elements\assets( $item );
		}
		$html = item( $kind, $item, $layout_id, $preview );
		if ( ! $preview && '' === trim( $html ) ) { continue; }
		$appearances[] = $item['appearance'];
		$marker = $preview ? ' data-builder-preview-item="' . esc_attr( $item['id'] ) . '"' : '';
		$presentation = array();
		if ( 'header' === $kind && 'container' !== $item['type'] ) {
			$marker .= ' data-rdct-header-color="' . esc_attr( \RadmanCT\Builder\HeaderColors\item_mode( $item ) ) . '"';
		}
		if ( in_array( $item['type'], array( 'logo', 'image' ), true ) ) {
			require_once __DIR__ . '/visuals.php';
			$presentation = \RadmanCT\Builder\Visuals\presentation( $item );
		}
		$markup .= '<div ' . \RadmanCT\Builder\appearance_attributes( $item['appearance'], false, $presentation ) . $marker . '>' . $html . '</div>';
	}
	if ( ! $preview && '' === $markup ) { return; }
	$appearance = $preview && ! $appearances ? $row['appearance'] : \RadmanCT\Builder\Responsive\collapse( $row['appearance'], $appearances );
	$marker = $preview ? ' data-builder-preview-row="' . esc_attr( $row['id'] ) . '"' : '';
	echo '<div ' . \RadmanCT\Builder\appearance_attributes( $appearance, true ) . $marker . '><div class="rdct-ctr rdct-bld__columns">' . $markup . '</div></div>';
}
