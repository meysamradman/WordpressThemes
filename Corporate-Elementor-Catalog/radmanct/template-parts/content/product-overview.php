<?php defined( 'ABSPATH' ) || exit; ?>
<div class="rdct-prd-overview">
	<?php get_template_part( 'template-parts/content/product-media' ); ?>
	<header class="rdct-prd-summary">
		<?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php the_title(); ?></h1><?php endif; ?>
		<?php if ( has_excerpt() ) : ?><div class="rdct-cnt-detail__intro"><?php the_excerpt(); ?></div><?php endif; ?>
		<?php get_template_part( 'template-parts/content/meta' ); ?>
		<?php get_template_part( 'template-parts/content/product-display' ); ?>
	</header>
</div>
