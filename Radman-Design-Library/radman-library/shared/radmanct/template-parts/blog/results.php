<?php defined('ABSPATH')||exit;global $wp_query;$posts=$wp_query->posts; ?>
<?php if(!$posts): ?><p><?php esc_html_e('No content found.','radmanct'); ?></p><?php else: ?>
<?php if(is_home()&&!is_paged()&&!get_search_query()): ?>
<section class="rdc-lead-grid" data-count="<?php echo min(3,count($posts)); ?>" aria-label="<?php esc_attr_e('Featured articles','radmanct'); ?>"><h2 class="screen-reader-text"><?php esc_html_e('Featured articles','radmanct'); ?></h2><?php foreach(array_slice($posts,0,3)as$index=>$entry)get_template_part('shared/radmanct/template-parts/blog/lead',null,array('primary'=>$index===0,'post_id'=>$entry->ID)); ?></section>
<?php if(count($posts)>3): ?><section class="rdc-blog-sec"><h2 class="rdc-blog-sec__ttl"><?php $categories=get_the_category($posts[3]->ID);echo esc_html($categories?$categories[0]->name:__('Latest articles','radmanct')); ?></h2><div class="rdc-blog-sec__grid"><?php foreach(array_slice($posts,3,4)as$index=>$entry)get_template_part('shared/radmanct/template-parts/blog/preview',null,array('variant'=>$index===0?'feature':'tile','post_id'=>$entry->ID)); ?></div></section><?php endif; ?>
<?php endif; ?>
<section class="rdc-jrn"><h2 class="rdc-jrn__ttl"><?php esc_html_e('Latest from the journal','radmanct'); ?></h2><div class="rdc-jrn__list">
<?php while(have_posts()):the_post();get_template_part('shared/radmanct/template-parts/blog/journal');endwhile; ?></div></section>
<?php the_posts_pagination();endif; ?>
