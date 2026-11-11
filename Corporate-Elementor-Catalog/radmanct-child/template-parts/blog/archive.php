<?php defined( 'ABSPATH' ) || exit; ?>
<?php $rdct_scope = \RadmanCT\Archives\scope(); ?>
<section class="rdct-ctr rdct-listing rdct-client-blog-archive" <?php if ( $rdct_scope ) { echo \RadmanCT\Archives\attributes( $rdct_scope ); } ?>>
	<?php get_template_part( 'template-parts/content/archive-header', null, array( 'scope' => 'blog' ) ); ?>
	<?php if ( $rdct_scope ) { \RadmanCT\Archives\status(); } ?>
	<div data-rdct-archive-intro><?php \RadmanCT\Archives\intro( 'blog' ); ?></div>
	<?php \RadmanCT\Sidebars\layout( static function (): void { ?>
		<div data-rdct-archive-view tabindex="-1"><?php \RadmanCT\Archives\view( 'blog', false ); ?></div>
	<?php }, $args['sidebar'] ?? array(), false ); ?>
</section>
