<?php
global $post;

if ( empty( $args ) ) {
	$args = array();
}
?>

<?php if ('' !== get_the_post_thumbnail()) : ?>
<div class="lqd-lp-img overflow-hidden border-radius-5 mt-4 mb-5">
    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
        <?php liquid_ajax_posts_entry_thumbnail( 'liquid-thumbnail', '', $args ); ?>
    </a>
</div>
<?php endif; ?>

<header class="entry-header lqd-lp-header mb-3">

	<div class="lqd-lp-meta lqd-lp-meta-dot-between text-uppercase font-weight-bold text-uppercase ltr-sp-1">
        <div class="lqd-lp-author pos-rel z-index-3">
            <div class="lqd-lp-author-info">
                <span class="mt-0 mb-0"><?php the_author_posts_link(); ?></span>
            </div>
        </div>
        <span class="lqd-lp-date">
            <a href="<?php the_permalink() ?>"><?php echo get_the_date(get_option('date_time')); ?></a>
        </span>

		<div class="lqd-lp-meta">
    		<?php liquid_ajax_posts_entry_tags('reset-ul inline-ul pos-rel z-index-3', $args ); ?>
    	</div>

        <?php if ( liquid_helper()->get_option( 'blog-post-modified-date' ) === 'yes' && get_the_date() != get_the_modified_date() ){ ?>
            <span class="lqd-lp-date">
                <a href="<?php the_permalink() ?>"><?php echo get_the_modified_date(get_option('date_time')); ?></a>
            </span>
		<?php } ?>
    </div>

    <?php liquid_ajax_posts_entry_title('mt-2 mb-3 h5', $args); ?>

</header>

<?php liquid_ajax_posts_entry_content('entry-summary lqd-lp-excerpt mb-3', $args); ?>

<?php liquid_ajax_posts_overlay_link(); ?>
