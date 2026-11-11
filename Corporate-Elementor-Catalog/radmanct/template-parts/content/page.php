<?php defined( 'ABSPATH' ) || exit; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'rdct-page rdct-ctr' ); ?>>
	<header class="rdct-page__header" <?php if ( \RadmanCT\PageTitle\active() ) { echo 'hidden'; } ?>><?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php the_title(); ?></h1><?php endif; ?></header>
	<div class="rdct-cnt"><?php the_content(); ?></div>
	<?php \RadmanCT\Templates\content_pagination(); ?>
</article>
