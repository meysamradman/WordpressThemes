<?php

if ( class_exists( 'Liquid_Elementor_Addons' ) ) {
	$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
	$page_settings_model   = $page_settings_manager->get_model( get_the_ID() );

	$sticky_share_view = $page_settings_model->get_settings( 'post_floating_box_social_style' );
	$sticky_share_view = $sticky_share_view ? $sticky_share_view : liquid_helper()->get_option( 'post-floating-box-social-style' );
	$author_in_sticky  = $page_settings_model->get_settings( 'post_floating_box_author_enable' );
	$author_in_sticky  = $author_in_sticky ? $author_in_sticky : liquid_helper()->get_option( 'post-floating-box-author-enable' );
} else {
	$sticky_share_view = liquid_helper()->get_option( 'post-floating-box-social-style' );
	$author_in_sticky  = liquid_helper()->get_option( 'post-floating-box-author-enable' );
}

$social_icons_classname = 'social-icon social-icon-vertical ';

if ( $sticky_share_view === 'with-text-outline' ) {
	$social_icons_classname .= 'reset-ul social-icon-sm social-icon-underline social-icon-with-label';
} else {
	$social_icons_classname .= 'reset-ul social-icon-lg';
}

$pinterest_image = wp_get_attachment_url( get_post_thumbnail_id(), 'full' );

$providers = liquid_helper()->get_option( 'social-share-providers' );

if ( empty( $providers['enabled'] ) ) {
	return;
}

?>
<div class="lqd-post-sticky-stuff">
	<div class="lqd-post-sticky-stuff-inner">
		<?php if ( 'on' == $author_in_sticky ) : ?>
			<div class="entry-meta">
				<div class="byline">
					<figure>
						<?php echo get_avatar( get_the_author_meta( 'user_email' ), 80 ); ?>
					</figure>
					<span>
						<span><?php esc_html_e( 'Author', 'hub' ); ?></span>
						<?php liquid_author_link( array( 'before' => '', ) ); ?>
					</span>
				</div>
			</div>
		<?php endif; ?>

		<div class="lqd-post-share">
			<?php if ( $sticky_share_view === 'with-text-outline' ) : ?>
				<span><?php esc_html_e( 'Share', 'hub' ); ?></span>
			<?php endif; ?>
			<ul class="<?php echo liquid_helper()->sanitize_html_classes( $social_icons_classname ); ?>">
				<?php
				get_template_part(
					'templates/blog/single/part',
					'share-list',
					array(
						'providers'  => $providers['enabled'],
						'show_label' => $sticky_share_view === 'with-text-outline',
					)
				);
				?>
			</ul>
		</div>
	</div>
</div>