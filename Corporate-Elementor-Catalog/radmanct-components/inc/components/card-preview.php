<?php

namespace RadmanCTComponents\Components\CardPreview;

defined( 'ABSPATH' ) || exit;

function video( int $id ): array {
	if ( $id < 1 || 'attachment' !== get_post_type( $id ) || ! wp_attachment_is( 'video', $id ) ) { return array(); }
	$mime = get_post_mime_type( $id );
	if ( ! in_array( $mime, array( 'video/mp4', 'video/webm', 'video/ogg' ), true ) ) { return array(); }
	$src = wp_get_attachment_url( $id );
	$uploads = wp_upload_dir( null, false );
	if ( ! $src || ! str_starts_with( $src, trailingslashit( $uploads['baseurl'] ) ) ) { return array(); }
	return array( 'id' => $id, 'src' => $src, 'mime' => $mime );
}

function frame( string $image, array $options ): array {
	$result = array( 'html' => $image, 'control' => '' );
	$video_id = filter_var( $options['video_id'] ?? 0, FILTER_VALIDATE_INT );
	$file = false !== $video_id ? video( $video_id ) : array();
	if ( ! $image || ! $file ) { return $result; }
	$runtime = \RadmanCTComponents\Assets\entry( 'src/js/components/card-preview-entry.js' );
	if ( ! $runtime ) { return $result; }
	$id = wp_unique_id( 'rdct-card-video-' );
	$mode = 'hover' === ( $options['mode'] ?? '' ) ? 'hover' : 'auto';
	$play_label = is_string( $options['play_label'] ?? null ) && '' !== trim( $options['play_label'] ) ? $options['play_label'] : __( 'Play video preview', 'radmanct-components' );
	$pause_label = is_string( $options['pause_label'] ?? null ) && '' !== trim( $options['pause_label'] ) ? $options['pause_label'] : __( 'Pause video preview', 'radmanct-components' );
	\RadmanCTComponents\Components\enqueue( 'card-preview' );
	if ( isset( $_GET['_rdct_fragment'] ) && ! wp_style_is( 'rdct-card-preview', 'done' ) ) { wp_print_styles( 'rdct-card-preview' ); }
	$result['html'] = '<span class="rdct-card-preview" data-rdct-card-preview data-mode="' . $mode . '" data-rdct-preview-runtime="' . esc_url( \RadmanCTComponents\Assets\url( $runtime['file'] ) ) . '">' . $image . '<video id="' . esc_attr( $id ) . '" muted loop playsinline preload="none" disablepictureinpicture disableremoteplayback aria-hidden="true" tabindex="-1" data-src="' . esc_url( $file['src'] ) . '" data-mime="' . esc_attr( $file['mime'] ) . '"></video></span>';
	$result['control'] = '<button type="button" class="rdct-card-preview-control" data-rdct-preview-control aria-controls="' . esc_attr( $id ) . '" aria-label="' . esc_attr( $play_label ) . '" data-play-label="' . esc_attr( $play_label ) . '" data-pause-label="' . esc_attr( $pause_label ) . '" hidden><span data-preview-play aria-hidden="true">' . \RadmanCTComponents\Icons\render( 'play', array( 'size' => 16, 'stroke' => 2 ) ) . '</span><span data-preview-pause aria-hidden="true" hidden>' . \RadmanCTComponents\Icons\render( 'pause', array( 'size' => 16, 'stroke' => 2 ) ) . '</span></button>';
	return $result;
}
