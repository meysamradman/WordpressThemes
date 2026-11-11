<?php

require __DIR__ . '/components-contract.php';
function wp_attachment_is_image( $id ) { return 7 === $id; }
function wp_get_attachment_image( $id, $size, $icon, $attributes ) { ++$GLOBALS['segment_image_reads']; return '<img class="' . $attributes['class'] . '" width="100" height="80" alt="Media alt" src="/uploads/local.jpg">'; }
$segment_image_reads = 0;
$segment_mask_reads = 0;
function wp_get_attachment_image_url( $id, $size ) { ++$GLOBALS['segment_mask_reads']; $GLOBALS['segment_mask_size'] = $size; return $GLOBALS['segment_mask_url'] ?? '/uploads/text-fill.jpg'; }
function esc_url_raw( $value, $protocols ) { return preg_match( '~^(?:https?://|/)~i', $value ) ? $value : ''; }
use RadmanCTComponents\Components\TextReveal;

$line_options = array( 'mode' => 'scroll', 'split_type' => 'lines', 'line_mask' => 'yes' );
foreach ( array( $line_options, $line_options + array( 'url' => 'https://radman.test/work' ) ) as $options ) {
	$line_html = TextReveal\render( 'Connected text', $options );
	expect( str_contains( $line_html, 'data-reveal-split="lines"' ) && str_contains( $line_html, 'data-reveal-mask="true"' ), 'Plain and styled render paths lost shared line settings.' );
}
expect( ! str_contains( TextReveal\render( 'Legacy text', array( 'mode' => 'scroll' ) ), 'data-reveal-split' ), 'Legacy reveal no longer defaults to words.' );
expect( ! str_contains( TextReveal\render( 'Static text', array( 'mode' => 'static', 'split_type' => 'lines', 'line_mask' => true ) ), 'data-reveal-split' ), 'Static text requested line animation.' );
expect( ! str_contains( TextReveal\render( 'Filled text', array( 'mode' => 'fill', 'split_type' => 'lines', 'line_mask' => true ) ), 'data-reveal-split' ), 'Line splitting leaked into word fill.' );
expect( '' === TextReveal\split_attributes( array( 'split_type' => 'lines" onclick="bad', 'line_mask' => array() ), 'scroll' ), 'Split attributes accepted arbitrary input.' );
$line_fields = TextReveal\fields();
expect( 'words' === $line_fields['split_type']['default'] && false === $line_fields['line_mask']['default'] && array( 'lines' ) === $line_fields['line_mask']['condition']['split_type'], 'Shared native controls lost defaults or dependent conditions.' );

$row = array( 'title' => ' [[Work]] ', 'image' => array( 'id' => 7 ), 'image_position' => 'end', 'color' => '#123abc', 'font_scale' => 120, 'font_weight' => '700' );
$html = TextReveal\render( '', array( 'mode' => 'rich', 'direction' => 'rtl', 'segments' => array( $row ), 'inline_image_width_responsive' => array( 'desktop' => 100, 'mobile' => 50 ), 'inline_image_radius_responsive' => array( 'mobile' => 0 ) ) );
expect( str_contains( $html, 'data-rdct-text-reveal="rich"' ) && str_contains( $html, 'dir="rtl"' ) && str_contains( $html, '<mark' ), 'Rich-only content or inherited text effects disappeared.' );
expect( strpos( $html, 'Work</mark>' ) < strpos( $html, '<img' ) && str_contains( $html, 'alt="Media alt"' ), 'Logical placement or native attachment markup changed.' );
expect( str_contains( $html, '--rdct-segment-scale:120%' ) && str_contains( $html, 'font-weight:700;' ) && str_contains( $html, 'color:#123abc;' ), 'Segment typography did not reach shared SSR.' );
expect( str_contains( $html, '--rdct-text-image-width:50px;' ) && str_contains( $html, '--rdct-text-image-radius:0px;' ), 'Mobile dimensions or explicit zero failed.' );
$row['title'] = '<script>bad</script>'; $row['image'] = array( 'url' => 'https://untrusted.test/image.svg' ); $row['color'] = 'red;display:none'; $row['font_family'] = 'evil;display:none'; $row['vertical_align'] = 'middle;display:none';
$html = TextReveal\render( '', array( 'mode' => 'rich', 'segments' => array( $row ) ) );
expect( str_contains( $html, '&lt;script&gt;' ) && ! str_contains( $html, '<script>' ) && ! str_contains( $html, 'display:none' ) && ! str_contains( $html, 'untrusted.test' ), 'Segment text, CSS or remote-image boundary failed.' );
$segment_image_reads = 0;
$html = TextReveal\render( '', array( 'mode' => 'rich', 'segments' => array_fill( 0, 50, array( 'image' => array( 'id' => 7 ) ) ) ) );
expect( 30 === $segment_image_reads && 30 === substr_count( $html, '<img' ), 'Segment count was not bounded before media work.' );
expect( str_contains( TextReveal\render( 'Fallback', array( 'mode' => 'rich', 'segments' => array( array( 'image' => array( 'id' => 999 ) ) ) ) ), 'Fallback' ), 'Removed attachment lost fallback text.' );
expect( array( 'text-reveal', 'text-effects', 'text-segments' ) === TextReveal\assets( array( 'mode' => 'rich' ) ), 'Rich text requested an animation dependency.' );
$gradient = TextReveal\render( '', array( 'mode' => 'rich', 'segments' => array( array( 'title' => 'Gradient', 'paint' => 'gradient', 'gradient_start' => 'red;display:none', 'gradient_end' => '#ffcc00', 'gradient_angle' => 999, 'image' => array( 'id' => 7 ), 'image_border_width' => 999, 'image_border_color' => 'var(--unsafe)' ) ) ) );
expect( str_contains( $gradient, 'data-paint="gradient"' ) && str_contains( $gradient, '--rdct-segment-gradient-start:#172b3a;' ) && str_contains( $gradient, '--rdct-segment-gradient-end:#ffcc00;' ) && str_contains( $gradient, '--rdct-segment-gradient-angle:360deg;' ), 'Gradient values were not bounded or did not reach the text-only wrapper.' );
expect( str_contains( $gradient, '--rdct-text-image-border-width:20px;' ) && str_contains( $gradient, '--rdct-text-image-border-color:#e6ecef;' ) && ! str_contains( $gradient, 'display:none' ) && ! str_contains( $gradient, 'unsafe' ), 'Inline image border accepted unbounded CSS.' );
$radial = TextReveal\segments( array( array( 'title' => 'Radial', 'paint' => 'gradient', 'gradient_type' => 'radial', 'gradient_start_position' => 12.5, 'gradient_end_position' => 85.5, 'gradient_position' => 'top left', 'gradient_position_tablet' => '', 'gradient_position_mobile' => 'bottom right' ) ) );
expect( str_contains( $radial, 'data-gradient-type="radial"' ) && str_contains( $radial, '--rdct-segment-gradient-start-position:12.5%;' ) && str_contains( $radial, '--rdct-segment-gradient-end-position:85.5%;' ), 'Radial gradient marker or decimal stops were lost.' );
expect( str_contains( $radial, '--rdct-segment-gradient-position:top left;' ) && str_contains( $radial, '--rdct-segment-gradient-position:bottom right;' ) && str_contains( $radial, '@media(max-width:767px)' ) && ! str_contains( $radial, '@media(max-width:1024px)' ), 'Radial position lost responsive overrides or inheritance.' );
expect( ! str_contains( $gradient, 'data-gradient-type=' ) && ! str_contains( $gradient, '--rdct-segment-gradient-start-position:' ), 'Legacy gradients no longer keep the linear default and implicit stops.' );
$unsafe_gradient = TextReveal\segments( array( array( 'title' => 'Safe gradient', 'paint' => 'gradient', 'gradient_type' => 'radial', 'gradient_start_position' => -10, 'gradient_end_position' => 999, 'gradient_position' => 'center; background:url(bad)' ) ) );
expect( str_contains( $unsafe_gradient, '--rdct-segment-gradient-start-position:0%;' ) && str_contains( $unsafe_gradient, '--rdct-segment-gradient-end-position:100%;' ) && ! str_contains( $unsafe_gradient, 'url(bad)' ) && ! str_contains( $unsafe_gradient, 'data-rdct-responsive' ), 'Gradient accepted an unsafe position or unbounded stops.' );
$unused_gradient = TextReveal\segments( array( array( 'title' => 'Solid', 'paint' => 'solid', 'gradient_type' => 'radial', 'gradient_position' => 'top left' ) ) );
expect( ! str_contains( $unused_gradient, 'data-gradient-type=' ) && ! str_contains( $unused_gradient, 'data-rdct-responsive' ) && ! str_contains( $unused_gradient, '--rdct-segment-gradient-' ), 'Unused gradient options emitted styles for solid text.' );
$plain = TextReveal\render( '', array( 'mode' => 'rich', 'segments' => array( array( 'title' => 'Existing', 'color' => '#123abc' ) ) ) );
expect( str_contains( $plain, 'color:#123abc;' ) && ! str_contains( $plain, 'data-paint="gradient"' ) && ! str_contains( $plain, '--rdct-text-image-border-width' ), 'Existing segment color or zero-border defaults changed.' );
foreach ( array( '#abc', '#abcd', '#abcdef', '#abcdef80', 'rgba(12,34,56,0)', 'rgba(12,34,56,.25)', 'rgb(12.5,34,56)', 'transparent' ) as $color ) {
	$normalized = TextReveal\segment_color( $color );
	expect( '' !== $normalized, 'Native picker color or alpha was rejected: ' . $color );
	$alpha = TextReveal\segments( array( array( 'title' => 'Alpha', 'color' => $color, 'paint' => 'gradient', 'gradient_start' => $color, 'gradient_end' => $color, 'image' => array( 'id' => 7 ), 'image_border_width' => 2, 'image_border_color' => $color ) ) );
	foreach ( array( 'color:', '--rdct-segment-gradient-start:', '--rdct-segment-gradient-end:', '--rdct-text-image-border-color:' ) as $property ) {
		expect( str_contains( $alpha, $property . $normalized . ';' ), 'Shared renderer discarded a native color for ' . $property );
	}
}
foreach ( array( null, array(), 123, '#12345', 'rgba(256,0,0,1)', 'rgba(0,0,0,-.1)', 'rgba(0,0,0,1.1)', 'rgb(-1,0,0)', 'rgb(255.1,0,0)', 'red;display:none', 'var(--unsafe)', 'url(evil)', "#abc\n;background:red", str_repeat( '0', 101 ) ) as $color ) {
	expect( 'fallback' === TextReveal\segment_color( $color, 'fallback' ), 'Invalid color crossed the shared CSS boundary.' );
}
expect( 'rgba(0,0,0,0)' === TextReveal\segment_color( 'rgba(0, 0, 0, 0)' ) && '#00000000' === TextReveal\segment_color( 'transparent' ), 'Explicit zero alpha was lost.' );
expect( ! in_array( 'text-segments', TextReveal\assets( array( 'mode' => 'static' ) ), true ), 'Rich-only CSS leaked into plain text.' );
$mask_row = array( 'title' => '[[Readable text]]', 'paint' => 'image', 'mask_image' => array( 'id' => 7 ), 'mask_size' => 'contain', 'mask_position' => 'start', 'mask_repeat' => 'repeat-x' );
$masked = TextReveal\segments( array( $mask_row ) );
TextReveal\segments( array( array_replace( $mask_row, array( 'mask_resolution' => 'client-banner' ) ) ) );
expect( 'client-banner' === $GLOBALS['segment_mask_size'], 'Registered custom image resolution was ignored.' );
TextReveal\segments( array( array_replace( $mask_row, array( 'mask_resolution' => '../../bad' ) ) ) );
expect( 'large' === $GLOBALS['segment_mask_size'], 'Invalid image resolution escaped the default.' );
$styled = TextReveal\segments( array( array( 'title' => 'Readable', 'font_style' => 'italic', 'text_transform' => 'uppercase', 'text_decoration' => 'underline', 'font_weight' => '300' ) ) );
expect( str_contains( $styled, 'font-style:italic;text-transform:uppercase;text-decoration:underline;' ) && str_contains( $styled, 'font-weight:300;' ) && str_contains( $styled, '>Readable</span>' ), 'Segment typography changed source text or lost safe styles.' );
$unsafe = TextReveal\segments( array( array( 'title' => 'Safe', 'font_style' => 'italic;display:none', 'text_transform' => array(), 'text_decoration' => 'url(evil)' ) ) );
expect( ! str_contains( $unsafe, 'display:none' ) && ! str_contains( $unsafe, 'url(evil)' ) && ! str_contains( $unsafe, 'font-style:' ), 'Invalid segment typography reached CSS.' );
expect( str_contains( $masked, 'data-paint="image" data-mask-position="start"' ) && str_contains( $masked, '--rdct-segment-mask-image:url(&quot;/uploads/text-fill.jpg&quot;);' ) && str_contains( $masked, '--rdct-segment-mask-size:contain;' ) && str_contains( $masked, '--rdct-segment-mask-repeat:repeat-x;' ) && str_contains( $masked, 'Readable text</mark>' ) && ! str_contains( $masked, '<img' ), 'Text mask lost its native attachment, safe CSS or readable text content.' );
$before_reads = $segment_mask_reads;
TextReveal\segments( array( array_replace( $mask_row, array( 'paint' => 'solid' ) ), array_replace( $mask_row, array( 'title' => '' ) ) ) );
expect( $before_reads === $segment_mask_reads, 'Inactive or textless mask fetched an unused attachment.' );
foreach ( array( array( 'id' => 999 ), array( 'url' => 'https://untrusted.test/mask.jpg' ), array( 'id' => array() ) ) as $missing ) {
    $fallback = TextReveal\segments( array( array_replace( $mask_row, array( 'mask_image' => $missing ) ) ) );
    expect( str_contains( $fallback, 'data-paint="solid"' ) && ! str_contains( $fallback, 'mask-image:' ) && ! str_contains( $fallback, 'untrusted.test' ), 'Missing or URL-only image made the text transparent or accepted a remote input.' );
}
$invalid_style = TextReveal\segments( array( array_replace( $mask_row, array( 'mask_size' => 'cover;display:none', 'mask_repeat' => array(), 'mask_position' => 'start" onclick="bad' ) ) ) );
expect( str_contains( $invalid_style, '--rdct-segment-mask-size:cover;' ) && str_contains( $invalid_style, '--rdct-segment-mask-repeat:no-repeat;' ) && str_contains( $invalid_style, 'data-mask-position="center"' ) && ! str_contains( $invalid_style, 'display:none' ), 'Mask layout accepted untrusted CSS.' );
foreach ( array( 'javascript:alert(1)', "/uploads/bad\nimage.jpg", 'data:image/svg+xml,test' ) as $invalid_url ) {
    $GLOBALS['segment_mask_url'] = $invalid_url;
    expect( ! str_contains( TextReveal\segments( array( $mask_row ) ), 'data-paint="image"' ), 'Unsafe attachment URL reached CSS.' );
}
unset( $GLOBALS['segment_mask_url'] );

$sized_row = array( 'title' => 'Sized', 'image' => array( 'id' => 7 ), 'image_width' => array( 'size' => 12.5, 'unit' => 'em' ), 'image_width_tablet' => array( 'size' => '', 'unit' => 'px' ), 'image_width_mobile' => array( 'size' => 50, 'unit' => '%' ), 'image_radius' => array( 'size' => 2, 'unit' => 'rem' ), 'image_radius_mobile' => array( 'size' => 0, 'unit' => 'px' ) );
$sized = TextReveal\segments( array( $sized_row ) );
expect( str_contains( $sized, 'inline-size:12.5em;' ) && str_contains( $sized, '--rdct-text-image-radius:2rem;' ) && str_contains( $sized, '@media(max-width:767px)' ) && str_contains( $sized, 'inline-size:50%;' ) && str_contains( $sized, '--rdct-text-image-radius:0px;' ) && ! str_contains( $sized, '@media(max-width:1024px)' ), 'Per-segment unit, responsive inheritance or explicit zero failed.' );
$typed_size = array( '$$type' => 'size', 'value' => array( 'size' => array( '$$type' => 'number', 'value' => 12.5 ), 'unit' => array( '$$type' => 'string', 'value' => 'em' ) ) );
$typed_sized = TextReveal\segments( array( array_replace( $sized_row, array( 'image_width' => $typed_size ) ) ) );
expect( str_contains( $typed_sized, 'inline-size:12.5em;' ), 'Native typed Size envelope lost its value.' );
$global_only = TextReveal\segments( array( array( 'title' => 'Inherited', 'image' => array( 'id' => 7 ) ) ) );
expect( ! str_contains( $global_only, 'data-rdct-responsive' ), 'Existing segments emit unnecessary layout overrides.' );
$corners = TextReveal\segments( array( array( 'title' => 'Corners', 'image' => array( 'id' => 7 ), 'image_radius' => array( 'size' => 10, 'unit' => 'px' ), 'image_radius_start_start' => array( 'size' => 25, 'unit' => '%' ), 'image_radius_mobile' => array( 'size' => 0, 'unit' => 'px' ), 'image_radius_end_end_mobile' => array( 'size' => 0, 'unit' => 'rem' ) ) ) );
expect( str_contains( $corners, '--rdct-text-image-radius-start-start:25%;' ) && str_contains( $corners, '--rdct-text-image-radius-end-end:0rem;' ) && str_contains( $corners, '--rdct-text-image-radius:0px;' ) && ! str_contains( $corners, 'border-radius:' ), 'Independent corners lost their unit/zero or were reset by a responsive shared shorthand.' );
$corner_css = file_get_contents( dirname( __DIR__ ) . '/radmanct-components/src/css/components/text/inline-image.css' );
foreach ( array( 'start-start', 'start-end', 'end-start', 'end-end' ) as $corner ) {
	expect( str_contains( $corner_css, 'border-' . $corner . '-radius: var(--rdct-text-image-radius-' . $corner . ', var(--rdct-text-image-radius, 12px))' ), 'A corner lost its logical shared fallback.' );
	$field = TextReveal\segment_fields()[ 'image_radius_' . str_replace( '-', '_', $corner ) ];
	expect( $field['size'] && '' === $field['default'] && 'image' === $field['group'], 'A corner created a default override or lost its native size/group metadata.' );
}
$disabled_corner = TextReveal\segments( array( array( 'title' => 'Disabled corner', 'image' => array( 'id' => 7 ), 'image_radius_start_start' => array( '$$type' => 'size', 'value' => array( 'size' => 20, 'unit' => 'px' ), 'disabled' => true ) ) ) );
expect( ! str_contains( $disabled_corner, 'data-rdct-responsive' ), 'Disabled native size incorrectly overrides the inherited corner.' );
$repeated_corners = array_fill( 0, 30, array( 'title' => 'Repeated', 'image' => array( 'id' => 7 ), 'image_radius_start_start' => array( 'size' => 20, 'unit' => '%' ) ) );
foreach ( array( TextReveal\segments( $repeated_corners ), TextReveal\segments( $repeated_corners ) ) as $fragment ) {
	expect( 1 === substr_count( $fragment, 'data-rdct-responsive' ) && 30 === substr_count( $fragment, 'class="rdct-text__image"' ), 'Identical segment styles were duplicated or a separately cached fragment lost its stylesheet.' );
}
$no_image = TextReveal\segments( array( array_replace( $sized_row, array( 'image' => array( 'id' => 999 ) ) ) ) );
expect( ! str_contains( $no_image, 'data-rdct-responsive' ), 'Unused image settings emitted CSS.' );
foreach ( array( array( 'size' => '3;display:none', 'unit' => 'px' ), array( 'size' => 10, 'unit' => 'px;background:url(bad)' ), array( 'size' => INF, 'unit' => 'px' ), array( 'size' => '', 'unit' => 'px' ) ) as $bad_size ) {
    $safe = TextReveal\segments( array( array( 'title' => 'Safe', 'image' => array( 'id' => 7 ), 'image_width' => $bad_size ) ) );
    expect( ! str_contains( $safe, 'data-rdct-responsive' ) && ! str_contains( $safe, 'display:none' ), 'Unsafe or empty size reached CSS.' );
    $safe_corner = TextReveal\segments( array( array( 'title' => 'Safe corner', 'image' => array( 'id' => 7 ), 'image_radius_start_end' => $bad_size ) ) );
    expect( ! str_contains( $safe_corner, 'data-rdct-responsive' ), 'An unsafe or empty corner reached CSS.' );
}

$edges = TextReveal\segments( array( array( 'title' => 'Edges', 'image' => array( 'id' => 7 ), 'image_border_width' => 3, 'image_border_start' => 0, 'image_border_end' => 8, 'image_border_top' => -1 ) ) );
$unit_edges = TextReveal\segments( array( array( 'image' => array( 'id' => 7 ), 'image_border_width' => .25, 'image_border_start' => 0, 'image_border_end' => .5, 'image_border_unit' => 'em', 'image_border_unit_tablet' => 'rem', 'image_border_width_mobile' => 0 ) ) );
expect( str_contains( $unit_edges, '--rdct-text-image-border-width:0.25em;' ) && str_contains( $unit_edges, '--rdct-text-image-border-end:0.5em;' ) && str_contains( $unit_edges, '--rdct-text-image-border-start:0em;' ) && str_contains( $unit_edges, '--rdct-text-image-border-width:0.25rem;' ) && str_contains( $unit_edges, '--rdct-text-image-border-width:0rem;' ), 'Unit-only device changes lost inherited widths, logical sides or zero.' );
$unsafe_unit = TextReveal\segments( array( array( 'image' => array( 'id' => 7 ), 'image_border_width' => 2, 'image_border_unit' => 'em;display:none', 'image_border_unit_mobile' => '%' ) ) );
expect( ! str_contains( $unsafe_unit, 'display:none' ) && ! str_contains( $unsafe_unit, '2%;' ), 'Invalid image border unit reached CSS.' );
expect( str_contains( $edges, '--rdct-text-image-border-width:3px;' ) && str_contains( $edges, '--rdct-text-image-border-start:0px;' ) && str_contains( $edges, '--rdct-text-image-border-end:8px;' ) && ! str_contains( $edges, '--rdct-text-image-border-top:' ), 'Logical edge overrides lost zero or inheritance.' );
$edge_only = TextReveal\segments( array( array( 'title' => 'Edge only', 'image' => array( 'id' => 7 ), 'image_border_top' => 4 ) ) );
expect( str_contains( $edge_only, '--rdct-text-image-border-top:4px;' ) && str_contains( $edge_only, '--rdct-text-image-border-width:0px;' ), 'Independent border requires the shared border to be enabled.' );
$device_border = TextReveal\segments( array( array( 'title' => 'Device border', 'image' => array( 'id' => 7 ), 'image_border_width' => 3, 'image_border_width_tablet' => 2.5, 'image_border_width_mobile' => 0, 'image_border_start' => 7, 'image_border_start_mobile' => 0, 'image_border_end_tablet' => -1, 'image_border_color' => '#12345680' ) ) );
expect( str_contains( $device_border, '--rdct-text-image-border-width:3px;' ) && str_contains( $device_border, '--rdct-text-image-border-width:2.5px;' ) && str_contains( $device_border, '--rdct-text-image-border-width:0px;' ), 'Shared border lost desktop, decimal tablet or explicit mobile zero.' );
expect( str_contains( $device_border, '--rdct-text-image-border-start:7px;' ) && str_contains( $device_border, '--rdct-text-image-border-start:0px;' ) && ! str_contains( $device_border, '--rdct-text-image-border-end:' ) && str_contains( $device_border, '@media(max-width:1024px)' ) && str_contains( $device_border, '@media(max-width:767px)' ), 'Logical device edges lost overrides or inheritance.' );
$mobile_only_border = TextReveal\segments( array( array( 'image' => array( 'id' => 7 ), 'image_border_top_mobile' => 4, 'image_border_color' => '#12345680' ) ) );
expect( str_contains( $mobile_only_border, '--rdct-text-image-border-top:4px;' ) && str_contains( $mobile_only_border, '--rdct-text-image-border-color:#12345680;' ), 'Mobile-only border did not retain the chosen color when desktop width is zero.' );
$inherit_border = TextReveal\segments( array( array( 'image' => array( 'id' => 7 ), 'image_border_width' => 3, 'image_border_width_tablet' => -1, 'image_border_width_mobile' => '', 'image_border_start_mobile' => array( '$$type' => 'number', 'value' => 4, 'disabled' => true ) ) ) );
expect( ! str_contains( $inherit_border, 'data-rdct-responsive' ), 'Empty, inherited or disabled border emitted an overriding stylesheet.' );
$safe_device_border = TextReveal\segments( array( array( 'image' => array( 'id' => 7 ), 'image_border_top_mobile' => 999, 'image_border_width_tablet' => '2;display:none', 'image_border_bottom_mobile' => INF ) ) );
expect( str_contains( $safe_device_border, '--rdct-text-image-border-top:20px;' ) && ! str_contains( $safe_device_border, 'display:none' ) && ! str_contains( $safe_device_border, 'INF' ), 'Device border accepted unsafe or unbounded CSS.' );
$no_image_border = TextReveal\segments( array( array( 'title' => 'No image', 'image_border_top_mobile' => 4 ) ) );
expect( ! str_contains( $no_image_border, 'data-rdct-responsive' ), 'Image-only responsive CSS was emitted for a segment without an image.' );
$border_fields = TextReveal\segment_fields();
expect( true === $border_fields['image_border_width']['responsive'] && true === $border_fields['image_border_start_mobile']['float'] && -1 === $border_fields['image_border_width_mobile']['default'], 'Shared native border metadata lost responsive registration, decimals or inheritance.' );
$unsafe_edges = TextReveal\segments( array( array( 'title' => 'Safe', 'image_border_start' => '2;display:none', 'image_border_end' => array() ) ) );
expect( ! str_contains( $unsafe_edges, 'display:none' ) && ! str_contains( $unsafe_edges, '--rdct-text-image-border-start:' ), 'Border accepted CSS injection.' );

$inherited_shadow = TextReveal\segments( array( array( 'title' => 'Existing' ) ) );
expect( ! str_contains( $inherited_shadow, 'text-shadow:' ), 'Old segments override inherited shadow.' );
$shadow_row = array( 'title' => 'Shadow text', 'image' => array( 'id' => 7 ), 'shadow_mode' => 'custom', 'shadow_horizontal' => -3, 'shadow_vertical' => 4, 'shadow_blur' => 8, 'shadow_color' => '#12345680' );
$shadow = TextReveal\segments( array( $shadow_row ) );
expect( str_contains( $shadow, 'text-shadow:-3px 4px 8px #12345680;' ) && str_contains( $shadow, 'data-paint="solid" style="text-shadow:' ), 'Scalar PHP/Atomic shadow was not scoped to text.' );
expect( ! preg_match( '/<(?:img|span class="rdct-text__figure")[^>]*text-shadow/', $shadow ), 'Text shadow affected the inline image.' );
$classic = TextReveal\segments( array( array( 'title' => 'Classic', 'shadow_mode' => 'custom', 'shadow' => array( 'horizontal' => -3, 'vertical' => 4, 'blur' => 8, 'color' => '#12345680' ) ) ) );
$relative_shadow = TextReveal\segments( array( array( 'title' => 'Relative shadow', 'shadow_mode' => 'custom', 'shadow_unit' => 'em', 'shadow_horizontal' => -.25, 'shadow_vertical' => .5, 'shadow_blur' => 1.2 ) ) );
$layered = TextReveal\segments( array( array( 'title' => 'Layered', 'shadow_mode' => 'custom', 'shadow_layers' => '2', 'shadow_horizontal' => 1, 'shadow_blur' => 0, 'shadow_2_horizontal' => -2, 'shadow_2_vertical' => 3, 'shadow_2_blur' => 4, 'shadow_2_color' => '#1234', 'shadow_2_unit' => 'rem', 'shadow_mode_tablet' => 'none', 'shadow_mode_mobile' => 'custom', 'shadow_layers_mobile' => '1', 'shadow_horizontal_mobile' => .25, 'shadow_vertical_mobile' => -.5, 'shadow_blur_mobile' => 1, 'shadow_unit_mobile' => 'em' ) ) );
expect( str_contains( $layered, 'text-shadow:1px 0px 0px #0000004d, -2rem 3rem 4rem #1234;' ) && str_contains( $layered, '--rdct-segment-shadow:none;' ) && str_contains( $layered, '--rdct-segment-shadow:0.25em -0.5em 1em #0000004d;' ) && str_contains( $layered, 'text-shadow:var(--rdct-segment-shadow,' ), 'Layered shadows or independent device overrides did not reach SSR.' );
$device_only = TextReveal\segments( array( array( 'title' => 'Only mobile', 'shadow_mode_mobile' => 'none' ) ) );
expect( str_contains( $device_only, 'text-shadow:var(--rdct-segment-shadow,inherit);' ) && str_contains( $device_only, '--rdct-segment-shadow:none;' ), 'Device-only shadow removal overrode the inherited desktop shadow.' );
expect( 2 === substr_count( TextReveal\segment_shadow_value( array( 'shadow_mode' => 'custom', 'shadow_layers' => 999 ) ), ', ' ), 'Shadow layer count is unbounded.' );
expect( str_contains( $relative_shadow, 'text-shadow:-0.25em 0.5em 1.2em ' ), 'Relative text shadow discarded decimal values or units.' );
$unsafe_shadow_unit = TextReveal\segments( array( array( 'title' => 'Safe shadow', 'shadow_mode' => 'custom', 'shadow_unit' => 'em;display:none', 'shadow_horizontal' => 2 ) ) );
expect( str_contains( $unsafe_shadow_unit, 'text-shadow:2px ' ) && ! str_contains( $unsafe_shadow_unit, 'display:none' ), 'Unsafe shadow unit reached CSS.' );
expect( str_contains( $classic, 'text-shadow:-3px 4px 8px #12345680;' ), 'Native Classic shadow differs from shared rendering.' );
$disabled = TextReveal\segments( array( array_replace( $shadow_row, array( 'shadow_mode' => 'none' ) ) ) );
expect( str_contains( $disabled, 'text-shadow:none;' ) && ! str_contains( $disabled, '#12345680' ), 'Explicit shadow removal failed.' );
expect( ! str_contains( TextReveal\segments( array( array_replace( $shadow_row, array( 'shadow_mode' => 'inherit' ) ) ) ), 'text-shadow:' ), 'Inherited shadow emitted custom styles.' );
$unsafe_shadow = TextReveal\segments( array( array_replace( $shadow_row, array( 'shadow_color' => 'red;display:none', 'shadow_horizontal' => NAN, 'shadow_vertical' => 1000, 'shadow_blur' => -8 ) ) ) );
expect( str_contains( $unsafe_shadow, 'text-shadow:0px 100px 0px #0000004d;' ) && ! str_contains( $unsafe_shadow, 'display:none' ), 'Unsafe shadow CSS or unbounded values accepted.' );
foreach ( array( 'rgba(0,0,0,0.3)' => 'rgba(0,0,0,0.3)', 'rgb(12, 34, 56)' => 'rgba(12,34,56,1)', 'rgba(999,0,0,1)' => '#0000004d', 'rgba(0,0,0,2)' => '#0000004d' ) as $color => $normalized ) {
    expect( str_contains( TextReveal\segment_shadow( array( 'shadow_mode' => 'custom', 'shadow' => array( 'color' => $color ) ) ), $normalized . ';' ), 'Native color alpha was lost or unsafe color accepted.' );
}

echo "Passed: rich SSR, logical inline media, bounded safe typography, responsive images and no animation dependency.\n";
$typed_text = TextReveal\segments( array( array( 'title' => 'Text', 'line_height' => array( 'size' => 1.4, 'unit' => 'em' ), 'letter_spacing' => array( 'size' => -1.2, 'unit' => 'px' ), 'letter_spacing_mobile' => array( 'size' => 0, 'unit' => 'px' ) ) ) );
$sized_text = TextReveal\segments( array( array( 'title' => 'Text', 'font_size' => array( 'size' => 2.5, 'unit' => 'vw' ), 'font_size_mobile' => array( 'size' => 18, 'unit' => 'px' ), 'word_spacing' => array( 'size' => -.2, 'unit' => 'em' ) ) ) );
expect( str_contains( $sized_text, 'font-size:2.5vw;' ) && str_contains( $sized_text, 'font-size:18px;' ) && str_contains( $sized_text, 'word-spacing:-0.2em;' ), 'Segment responsive typography lost units or negative word spacing.' );
$custom_mask = TextReveal\segments( array( array_replace( $mask_row, array( 'mask_size' => 'custom', 'mask_position' => 'custom', 'mask_width' => array( 'size' => 150, 'unit' => '%' ), 'mask_x' => array( 'size' => -20, 'unit' => 'px' ), 'mask_y_mobile' => array( 'size' => 0, 'unit' => '%' ), 'mask_attachment' => 'fixed' ) ) ) );
expect( str_contains( $custom_mask, 'data-mask-size="custom"' ) && str_contains( $custom_mask, '--rdct-segment-mask-width:150%;' ) && str_contains( $custom_mask, '--rdct-segment-mask-x:-20px;' ) && str_contains( $custom_mask, '--rdct-segment-mask-y:0%;' ) && str_contains( $custom_mask, '--rdct-segment-mask-attachment:fixed;' ), 'Custom image fill lost physical coordinates, dimensions or attachment.' );
expect( str_contains( $typed_text, 'line-height:1.4em;' ) && str_contains( $typed_text, 'letter-spacing:-1.2px;' ) && str_contains( $typed_text, 'letter-spacing:0px;' ) && str_contains( $typed_text, '@media(max-width:767px)' ), 'Responsive text typography lost units, negative spacing or explicit zero.' );
$inherited_text = TextReveal\segments( array( array( 'title' => 'Existing' ) ) );
expect( ! str_contains( $inherited_text, 'data-rdct-responsive' ), 'Existing text acquired unnecessary typography overrides.' );
$unsafe_type = TextReveal\segments( array( array( 'title' => 'Safe', 'line_height' => array( 'size' => 99999, 'unit' => 'em' ), 'letter_spacing' => array( 'size' => 'bad()', 'unit' => 'px' ) ) ) );
expect( str_contains( $unsafe_type, 'line-height:10em;' ) && ! str_contains( $unsafe_type, 'letter-spacing:' ) && ! str_contains( $unsafe_type, 'bad()' ), 'Typography accepted unsafe input or ignored unit-specific bounds.' );
