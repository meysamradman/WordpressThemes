<?php defined( 'ABSPATH' ) || exit; ?>
<div class="rdct-empty-state">
	<?php if ( ! empty( $args['catalog'] ) ) : ?>
		<p><?php esc_html_e( 'No products match these filters. Change or clear the filters to try again.', 'radmanct' ); ?></p>
	<?php elseif ( ! empty( $args['portfolio'] ) ) : ?>
		<p><?php esc_html_e( 'No projects found.', 'radmanct' ); ?></p>
	<?php elseif ( ! empty( $args['blog'] ) ) : ?>
		<p><?php esc_html_e( 'No posts match these filters. Change or clear the filters to try again.', 'radmanct' ); ?></p>
	<?php else : ?>
		<p><?php esc_html_e( 'No content found.', 'radmanct' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div>
