<?php

extract( $atts );

if( function_exists( 'ld_helper' ) ) {
	$filter_cats = ld_helper()->terms_are_ids_or_slugs( $filter_cats, 'category' );
}

$terms = get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => false,
	'include'    => $filter_cats
) );

if( empty( $terms ) ) {
	return;
}

$active_term_id    = isset( $active_term_id ) ? (int) $active_term_id : 0;
$grid_filter_param = isset( $grid_filter_param ) ? (string) $grid_filter_param : '';
$term_counts       = isset( $term_counts ) && is_array( $term_counts ) ? $term_counts : array();

$filter_wrapper = array(
	'liquid-filter-items'

);

$filter_classnames = array(
	'filter-list',
	'filter-list-style-1',
	'ltr-sp-1',
	'hidden-xs',
	'hidden-sm',
	$filter_color,
	$filter_size,
	$filter_decoration === 'filters-underline' || $filter_decoration === 'filters-underline' ? 'filter-list-decorated' : '',
	$filter_decoration,
	$filter_transformation,
	$filter_weight,
);

$filter_title_classnames = array(
	'liquid-filter-items-label',
	'mb-0',
	$filter_title_size,
	$filter_title_weight,
	$filter_title_transformation
);

$wrapper_data_attr = '';
if ( ! empty( $term_counts ) ) {
	$wrapper_data_attr = " data-term-counts='" . esc_attr( wp_json_encode( $term_counts ) ) . "'";
}

$filter_url = function ( $term_id ) use ( $grid_filter_param ) {
	if ( empty( $grid_filter_param ) ) {
		return '#';
	}
	if ( $term_id <= 0 ) {
		return esc_url( remove_query_arg( $grid_filter_param ) );
	}
	return esc_url( add_query_arg( $grid_filter_param, (int) $term_id ) );
};

$counter_html = function ( $key ) use ( $term_counts ) {
	if ( ! array_key_exists( $key, $term_counts ) ) {
		return '';
	}
	return '<sup class="lqd-filter-counter"><span>' . (int) $term_counts[ $key ] . '</span></sup>';
};

?>
<div class="col-lg-5 col-xs-12">

	<?php if( !empty( $filter_subtitle ) ) { ?>
	<header>
		<?php if( !empty( $filter_subtitle ) ) { ?>
			<h6 class="text-uppercase"><?php echo wp_kses_post( $filter_subtitle ); ?></h6>
		<?php } ?>
	</header>
	<?php } ?>

	<?php if( !empty( $filter_subtitle ) ) { ?>
	<h6 class="d-inline-flex mt-0 text-uppercase ltr-sp-1"><?php echo wp_kses_post( $filter_subtitle ); ?></h6>
	<?php } ?>
	<?php if( !empty( $filter_title ) ) { ?>
	<h2 class="mt-2 mb-sm-6"><?php echo wp_kses_post( do_shortcode( $filter_title ) ); ?></h2>
	<?php } ?>

	<div class="liquid-filter-items align-items-start"<?php echo $wrapper_data_attr; ?>>
		<div class="liquid-filter-items-inner flex-sm-column">

			<ul class="<?php echo join( ' ', $filter_classnames ); ?>" id="<?php echo esc_attr( $filter_id ); ?>">
				<li class="<?php echo $active_term_id === 0 ? 'active' : ''; ?>" data-filter="*" data-id="0">
					<?php if ( ! empty( $grid_filter_param ) ) : ?>
						<a href="<?php echo $filter_url( 0 ); ?>"><span><?php echo esc_html( $filter_lbl_all ); ?><?php echo $counter_html( '*' ); ?></span></a>
					<?php else : ?>
						<span><?php echo esc_html( $filter_lbl_all ); ?><?php echo $counter_html( '*' ); ?></span>
					<?php endif; ?>
				</li>
				<?php foreach( $terms as $term ) :
					$is_active = ( $term->term_id === $active_term_id ) ? 'active' : '';
					$counter   = $counter_html( $term->term_id );
					?>
					<li class="<?php echo esc_attr( $is_active ); ?>" data-filter=".lqd-f-<?php echo esc_attr( $term->term_id ); ?>" data-id="<?php echo esc_attr( $term->term_id ); ?>">
						<?php if ( ! empty( $grid_filter_param ) ) : ?>
							<a href="<?php echo $filter_url( $term->term_id ); ?>"><span><?php echo esc_html( $term->name ); ?><?php echo $counter; ?></span></a>
						<?php else : ?>
							<span><?php echo esc_html( $term->name ); ?><?php echo $counter; ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="lqd-filter-dropdown visible-xs visible-sm" data-form-options='{ "dropdownAppendTo": "self" }'>
				<select name="lqd-filter-dropdown-<?php echo esc_attr( $filter_id ); ?>" id="lqd-filter-dropdown<?php echo esc_attr( $filter_id ); ?>">
					<option <?php selected( $active_term_id, 0 ); ?> value="*" data-filter="*"><?php echo esc_html( $filter_lbl_all ); ?></option>
					<?php foreach( $terms as $term ) : ?>
						<option <?php selected( $active_term_id, $term->term_id ); ?> value=".<?php echo esc_attr( $term->slug ); ?>" data-filter=".<?php echo esc_attr( $term->slug ); ?>" data-id="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

		</div>
	</div>

	<?php
		if ( !defined('ELEMENTOR_VERSION') ){
			$this->get_button();
		}
	?>

</div>
