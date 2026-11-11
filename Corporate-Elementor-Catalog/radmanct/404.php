<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<section class="rdct-ctr rdct-empty-state">
	<h1><?php esc_html_e( 'Page not found', 'radmanct' ); ?></h1>
	<p><?php esc_html_e( 'Try a search or return to the home page.', 'radmanct' ); ?></p>
	<?php get_search_form(); ?>
	<a class="rdct-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'radmanct' ); ?></a>
</section>
<?php get_footer(); ?>
