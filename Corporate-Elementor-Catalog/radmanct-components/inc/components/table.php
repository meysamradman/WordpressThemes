<?php

namespace RadmanCTComponents\Components\Table;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/presentation.php';

function fields(): array {
	return array(
		'caption' => array( 'label' => __( 'Table caption', 'radmanct-components' ), 'default' => '' ),
		'variant' => array( 'label' => __( 'Style', 'radmanct-components' ), 'default' => 'grid', 'options' => array( 'grid' => __( 'Grid', 'radmanct-components' ), 'striped' => __( 'Striped', 'radmanct-components' ), 'minimal' => __( 'Minimal', 'radmanct-components' ) ) ),
		'header' => array( 'label' => __( 'Show column headings', 'radmanct-components' ), 'default' => true ),
		'row_headers' => array( 'label' => __( 'First column as row headings', 'radmanct-components' ), 'default' => false ),
		'align' => array( 'label' => __( 'Text alignment', 'radmanct-components' ), 'default' => 'start', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ) ) ),
		'min_width' => array( 'label' => __( 'Minimum table width (px)', 'radmanct-components' ), 'default' => 600, 'min' => 0, 'max' => 2400 ),
		'padding' => array( 'label' => __( 'Cell padding (px)', 'radmanct-components' ), 'default' => 20, 'min' => 0, 'max' => 64 ),
		'head_background' => array( 'label' => __( 'Header background', 'radmanct-components' ), 'default' => '#f1f7f6', 'color' => true ),
		'line_color' => array( 'label' => __( 'Border color', 'radmanct-components' ), 'default' => '#e6ecef', 'color' => true ),
	);
}

function sample(): array {
	return array( 'columns' => array( __( 'Feature', 'radmanct-components' ), __( 'Value', 'radmanct-components' ) ), 'rows' => array( array( 'cells' => array( __( 'Title', 'radmanct-components' ), __( 'Description', 'radmanct-components' ) ) ), array( 'cells' => array( __( 'Material', 'radmanct-components' ), __( 'Your content', 'radmanct-components' ) ) ) ) );
}

function settings( array $options ): array {
	$result = array();
	foreach ( fields() as $key => $field ) {
		$value = $options[ $key ] ?? $field['default'];
		if ( isset( $field['options'] ) ) { $value = is_string( $value ) && isset( $field['options'][ $value ] ) ? $value : $field['default']; }
		elseif ( is_bool( $field['default'] ) ) { $value = in_array( $value, array( true, 'yes', 'true' ), true ); }
		elseif ( isset( $field['min'] ) ) { $value = is_numeric( $value ) && is_finite( (float) $value ) ? max( $field['min'], min( $field['max'], (int) $value ) ) : $field['default']; }
		elseif ( ! empty( $field['color'] ) ) { $value = is_string( $value ) && preg_match( '/^#[a-f0-9]{6}$/iD', $value ) ? $value : $field['default']; }
		else { $value = is_string( $value ) ? sanitize_text_field( substr( $value, 0, 500 ) ) : ''; }
		$result[ $key ] = $value;
	}
	return $result;
}

function text( $value, int $length ): string { return is_string( $value ) && preg_match( '/^.{0,' . $length . '}/us', $value, $match ) ? $match[0] : ''; }

function data( array $input ): array {
	$columns = is_array( $input['columns'] ?? null ) ? array_slice( array_values( $input['columns'] ), 0, 12 ) : array();
	$columns = array_map( static fn( $value ) => sanitize_text_field( text( $value, 500 ) ), $columns );
	$rows = array(); $budget = 131072;
	$allowed = array( 'a' => array( 'href' => true, 'title' => true ), 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'small' => array(), 'br' => array(), 'code' => array() );
	foreach ( array_slice( is_array( $input['rows'] ?? null ) ? $input['rows'] : array(), 0, 100 ) as $row ) {
		if ( ! is_array( $row ) || ! is_array( $row['cells'] ?? null ) ) { continue; }
		$cells = array();
		foreach ( $columns as $index => $column ) {
			$raw = text( $row['cells'][ $index ] ?? null, 3000 );
			if ( strlen( $raw ) > $budget ) { $raw = ''; $budget = 0; }
			$budget -= strlen( $raw );
			$cells[] = wp_kses( nl2br( $raw, false ), $allowed );
		}
		$rows[] = array( 'cells' => $cells );
		if ( $budget <= 0 ) { break; }
	}
	return array( 'columns' => $columns, 'rows' => $rows );
}

function render( array $input, array $options = array() ): string {
	$data = data( $input );
	if ( ! $data['columns'] || ! $data['rows'] ) { return ''; }
	$s = settings( $options );
	$label = $s['caption'] ?: __( 'Data table', 'radmanct-components' );
	$style = '--rdct-tb-min:' . $s['min_width'] . 'px;--rdct-tb-pad:' . $s['padding'] . 'px;--rdct-tb-head:' . $s['head_background'] . ';--rdct-tb-line:' . $s['line_color'] . ';';
	$html = '<div class="rdct-tb rdct-tb--' . $s['variant'] . ' rdct-tb--' . $s['align'] . '" role="region" tabindex="0" aria-label="' . esc_attr( $label ) . '" style="' . esc_attr( $style ) . '"><table>';
	$html .= '<caption' . ( '' === $s['caption'] ? ' class="rdct-tb__caption-hidden"' : '' ) . '>' . esc_html( $label ) . '</caption>';
	if ( $s['header'] ) { $html .= '<thead><tr>'; foreach ( $data['columns'] as $column ) { $html .= '<th scope="col">' . esc_html( $column ) . '</th>'; } $html .= '</tr></thead>'; }
	$html .= '<tbody>';
	foreach ( $data['rows'] as $row ) {
		$html .= '<tr>';
		foreach ( $row['cells'] as $index => $cell ) { $heading = 0 === $index && $s['row_headers']; $html .= $heading ? '<th scope="row">' : '<td>'; $html .= $cell . ( $heading ? '</th>' : '</td>' ); }
		$html .= '</tr>';
	}
	return \RadmanCTComponents\Components\Presentation\render( $html . '</tbody></table></div>', $options, 'table' );
}
