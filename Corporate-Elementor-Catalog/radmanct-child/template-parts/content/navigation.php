<?php
defined( 'ABSPATH' ) || exit;
$rdct_content_id = $args['instance_id'];
?>
<nav class="rdct-client-navigation" aria-labelledby="<?php echo esc_attr( $rdct_content_id ); ?>">
	<h2 id="<?php echo esc_attr( $rdct_content_id ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h2>
	<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_id' => $rdct_content_id . '-menu', 'fallback_cb' => false, 'depth' => 2 ) ); ?>
</nav>
