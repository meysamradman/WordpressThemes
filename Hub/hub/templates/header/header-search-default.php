<?php

$search_id = uniqid('search-');
$icon      = 'lqd-icn-ess icon-ld-search';
$ajax_search = $atts['ajax_search'] ?? false;

if (class_exists('Liquid_Elementor_Addons') && defined('ELEMENTOR_VERSION')) {
	$icon = isset($icon_render) ? $icon_render : $icon;
}

if (!isset($search_type)) {
	if (class_exists('WooCommerce')) $search_type = "product";
	else $search_type = "post";
}

if ( ! isset( $custom_search_type ) ) {
	$custom_search_type = '';
}

?>
<div class="ld-module-search lqd-module-search-default d-flex align-items-center pos-rel" data-ajax-search-module>

	<span class="ld-module-trigger collapsed" role="button" data-ld-toggle="true" data-toggle="collapse" data-target="<?php echo '#' . esc_attr($search_id); ?>" data-bs-toggle="collapse" data-bs-target="<?php echo '#' . esc_attr($search_id); ?>" aria-controls="<?php echo esc_attr($search_id) ?>" aria-expanded="false" aria-label="<?php echo esc_attr_e('Search', 'hub') ?>">
		<span class="ld-module-trigger-txt"></span>
		<span class="ld-module-trigger-icon">
			<i class="<?php echo esc_attr($icon) ?>"></i>
		</span>
	</span>

	<div role="search" class="ld-module-dropdown collapse pos-abs" id="<?php echo esc_attr($search_id) ?>">
		<div class="ld-search-form-container">
			<form
				role="search"
				method="get"
				action="<?php echo esc_url(home_url('/')) ?>"
				class="ld-search-form pos-rel"
				<?php echo $ajax_search ? 'data-ajax-search' : ''; ?>
			>
				<label class="screen-reader-text" for="s"><?php esc_html_e('Search', 'hub') ?></label>
				<div class="ld-search-form__input-wrapper">
					<input class="w-100" type="search" placeholder="<?php echo esc_attr_x('Start searching', 'placeholder', 'hub') ?>" value="<?php echo get_search_query() ?>" name="s" />
					<span role="button" class="input-icon d-inline-block pos-abs" data-ld-toggle="true" data-toggle="collapse" data-target="<?php echo '#' . esc_attr($search_id); ?>" data-bs-toggle="collapse" data-bs-target="<?php echo '#' . esc_attr($search_id); ?>" aria-controls="<?php echo esc_attr($search_id) ?>" aria-expanded="false">
						<i class="lqd-icn-ess icon-ld-search"></i>
					</span>
					<?php if ( $ajax_search ): ?>
						<span data-search-loader class="ld-loader"></span>
					<?php endif; ?>
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
		</div>
	</div>

</div>