<?php
namespace RadmanCTComponents\PageTitle;
defined( 'ABSPATH' ) || exit;

function render( string $title, array $crumbs, array $options ): string {
	$s = \RadmanCTComponents\Background\normalize( $options['background'] ?? array() ) ?? \RadmanCTComponents\Background\normalize( array() );
	$options['height'] = max( 120, min( 900, (int) ( $options['height'] ?? 280 ) ) );
	$options['overlay'] = is_string( $options['overlay'] ?? null ) && preg_match( '/^#[a-f0-9]{6}$/iD', $options['overlay'] ) ? $options['overlay'] : '#000000';
	$options['overlayOpacity'] = is_numeric( $options['overlayOpacity'] ?? null ) ? max( 0, min( 1, (float) $options['overlayOpacity'] ) ) : 0;
	$options['breadcrumbs'] = true === ( $options['breadcrumbs'] ?? true );
	$shape = 'notched' === ( $options['shape'] ?? '' ) ? 'notched' : 'plain';
	$paint = \RadmanCTComponents\Background\paint( $s );
	$style = '--rdct-title-paint:' . $paint . ';--rdct-title-base:' . ( $s['color'] ?: 'var(--rdct-paper,#fff)' ) . ';--rdct-title-height:' . (int) $options['height'] . 'px;';
	$style .= '--rdct-title-overlay:' . $options['overlay'] . ';--rdct-title-overlay-opacity:' . $options['overlayOpacity'] . ';';
	$surface = \RadmanCTComponents\Background\normalize( $options['surface'] ?? array(), array( 'color', 'gradient' ) );
	$style .= '--rdct-title-surface:' . \RadmanCTComponents\Background\paint( $surface ?? array(), 'transparent' ) . ';';
	$image = '';
	if ( in_array( $s['type'], array( 'image', 'video' ), true ) && $s['image'] && wp_attachment_is_image( $s['image'] ) ) {
		$image = wp_get_attachment_image( $s['image'], 'full', false, array( 'alt' => '', 'loading' => 'eager', 'decoding' => 'async', 'sizes' => '100vw' ) );
	}
	$media = array( 'html' => $image, 'control' => '' );
	if ( 'video' === $s['type'] ) { $media = \RadmanCTComponents\Components\CardPreview\frame( $image ?: '<span aria-hidden="true"></span>', array( 'video_id' => $s['video'], 'mode' => 'auto', 'play_label' => __( 'Play background video', 'radmanct-components' ), 'pause_label' => __( 'Pause background video', 'radmanct-components' ) ) ); }
	$html = '<section class="rdct-page-title rdct-card-preview-owner" data-rdct-page-title data-title-shape="' . $shape . '" style="' . esc_attr( $style ) . '" aria-labelledby="rdct-page-title-heading">';
	$html .= '<div class="rdct-page-title__body">';
	if ( $media['html'] ) { $html .= '<div class="rdct-page-title__media" aria-hidden="true">' . $media['html'] . '</div>'; }
	$html .= '<div class="rdct-page-title__inner rdct-ctr"><h1 id="rdct-page-title-heading">' . esc_html( $title ) . '</h1></div>' . $media['control'] . '</div>';
	if ( $options['breadcrumbs'] && $crumbs ) {
		$html .= '<nav class="rdct-page-title__trail" aria-label="' . esc_attr__( 'Breadcrumbs', 'radmanct-components' ) . '"><ol>';
		foreach ( array_slice( $crumbs, 0, 32 ) as $index => $crumb ) {
			$last = count( $crumbs ) - 1 === $index;
			$html .= '<li>' . ( ! $last && ! empty( $crumb['url'] ) ? '<a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['label'] ) . '</a>' : '<span aria-current="page">' . esc_html( $crumb['label'] ) . '</span>' ) . '</li>';
		}
		$html .= '</ol></nav>';
	}
	return $html . '</section>';
}
