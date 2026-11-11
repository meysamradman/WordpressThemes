<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'rdct-site' ); ?> data-table-label="<?php esc_attr_e( 'Scrollable table', 'radmanct' ); ?>">
<?php wp_body_open(); ?>
<a class="rdct-skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'radmanct' ); ?></a>
<?php do_action( 'radmanct/before_site' ); ?>
<?php get_template_part( 'template-parts/site/header' ); ?>
<div data-rdct-title-area><?php \RadmanCT\PageTitle\render(); ?></div>
<main id="main-content" tabindex="-1" <?php echo \RadmanCT\Layout\content_attributes(); ?>>
