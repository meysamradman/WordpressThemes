<?php defined( 'ABSPATH' ) || exit;
if ( \RadmanCT\PageTitle\active() && ( ! is_archive() || ! get_the_archive_description() ) ) { echo '<header data-rdct-archive-header hidden></header>'; return; }
?>
<header data-rdct-archive-header<?php if ( 'blog' === ( $args['scope'] ?? '' ) ) { echo ' class="rdct-listing__header"'; } ?>><?php if ( ! \RadmanCT\PageTitle\active() ) : ?><h1><?php
if ( is_search() || 'blog' === ( $args['scope'] ?? '' ) ) { \RadmanCT\Templates\title(); }
elseif ( is_tax() ) { single_term_title(); }
else { post_type_archive_title(); }
?></h1><?php endif; ?>
	<?php if ( is_archive() ) { the_archive_description( '<div class="rdct-cnt">', '</div>' ); } ?>
</header>
