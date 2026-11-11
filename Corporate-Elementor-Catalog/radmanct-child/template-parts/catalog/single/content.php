<?php defined( 'ABSPATH' ) || exit; ?>
<?php if ( '' !== trim( get_the_content() ) ) : ?><h2 class="rdct-prd-description-title"><?php esc_html_e( 'Product description', 'radmanct' ); ?></h2><?php endif; ?>
<div class="rdct-cnt"><?php the_content(); ?></div>
<?php \RadmanCT\Templates\content_pagination(); ?>
