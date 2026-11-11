<?php
$description = $atts['description'];
$scheme = $atts['scheme'];
$icon_text =  $atts['icon_text'];
$icon_text_align =  $atts['icon_text_align'];
$show_icon =  $atts['show_icon'];
$icon_style =  $atts['icon_style'];
$ajax_search = $atts['ajax_search'] ?? false;

$trigger_class = array(
	'ld-module-trigger',
	'collapsed',
	$icon_text_align,
	$show_icon,
	$icon_style,
);

if (defined('ELEMENTOR_VERSION') && is_callable('Elementor\Plugin::instance')) {
	$icon = !empty($icon_render) ? $icon_render : 'lqd-icn-ess icon-ld-search';
} else {
	$icon_opts = liquid_get_icon($atts);
	$icon      = !empty($icon_opts['type']) && !empty($icon_opts['icon']) ? $icon_opts['icon'] : 'lqd-icn-ess icon-ld-search';
}

if (!isset($search_type)) {
	if (class_exists('WooCommerce')) $search_type = "product";
	else $search_type = "post";
}

$custom_search_type = $custom_search_type ?? '';

$module_classes = [ 'ld-module-search lqd-module-search-slide-top d-flex align-items-center' ];

if ( ! empty( $scheme ) ) {
	$module_classes[] = $scheme;
}

if ( ! empty( $ajax_search ) ) {
	$module_classes[] = 'ld-has-ajax-search';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $module_classes ) ); ?>" data-module-style='lqd-search-style-slide-top'>

	<?php
	$search_id = uniqid('search-');
	?>

	<span class="<?php echo liquid_helper()->sanitize_html_classes($trigger_class) ?>" role="button" data-ld-toggle="true" data-toggle="collapse" data-bs-toggle="collapse" data-target="<?php echo '#' . esc_attr($search_id); ?>" data-bs-target="<?php echo '#' . esc_attr($search_id); ?>" aria-controls="<?php echo esc_attr($search_id) ?>" aria-expanded="false" aria-label="<?php echo esc_attr_e('Search', 'hub') ?>">
		<span class="ld-module-trigger-txt">
			<?php echo do_shortcode($icon_text) ?>
		</span>
		<?php if ('lqd-module-show-icon' === $show_icon) { ?>
			<span class="ld-module-trigger-icon">
				<i class="<?php echo esc_attr($icon) ?>"></i>
			</span>
		<?php } ?>
	</span>

	<div class="ld-module-dropdown collapse d-flex w-100 flex-column pos-fix overflow-hidden backface-hidden" id="<?php echo esc_attr($search_id) ?>" data-ajax-search-module>

		<div class="ld-search-form-container d-flex flex-column justify-content-center h-100 mx-auto backface-hidden">
			<form
				role="search"
				method="get"
				action="<?php echo esc_url(home_url('/')) ?>"
				class="ld-search-form w-100"
				<?php echo $ajax_search ? 'data-ajax-search' : ''; ?>
			>
				<label class="screen-reader-text" for="s"><?php esc_html_e('Search', 'hub') ?></label>
				<div class="ld-search-form-input-wrapper">
					<input class="w-100" type="search" placeholder="<?php echo esc_attr_x('Search', 'placeholder', 'hub') ?>" value="<?php echo get_search_query() ?>" name="s">
					<span role="button" class="input-icon d-inline-flex align-items-center justify-content-center pos-abs" data-ld-toggle="true" data-toggle="collapse" data-bs-toggle="collapse" data-target="<?php echo '#' . esc_attr($search_id); ?>" data-bs-target="<?php echo '#' . esc_attr($search_id); ?>" aria-controls="<?php echo esc_attr($search_id) ?>" aria-expanded="false" aria-label="<?php echo esc_attr_e('Close', 'hub') ?>">
						<i class="lqd-icn-ess icon-ld-search"></i>
						<?php if ( $ajax_search ): ?>
							<span data-search-loader class="ld-loader"></span>
						<?php endif; ?>
					</span>
				</div>
				<input type="hidden" name="post_type" value="<?php echo esc_attr($search_type); ?>" />
				<input type="hidden" name="custom_post_type" value="<?php echo esc_attr($custom_search_type); ?>" />
				<?php if ( $ajax_search ): ?>
					<div class="liquid-ajax-search-results woocommerce">
						<ul class="reset-ul" data-search-results>
						</ul>
					</div>
				<?php endif; ?>
			</form>
			<?php if (!empty($description)) { ?>
				<p class="lqd-module-search-info"><?php echo esc_html($description); ?></p>
			<?php } ?>
		</div>

	</div>

</div>