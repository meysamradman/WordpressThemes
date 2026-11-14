<?php defined('ABSPATH')||exit; ?>
<aside class="rdc-cnt-sb" aria-label="<?php esc_attr_e('Blog sidebar','radmanct'); ?>"><div class="rdc-blog-sb__cnt">
<section class="rdc-wdg"><h2 class="rdc-wdg__ttl"><?php esc_html_e('Search articles','radmanct'); ?></h2><?php get_search_form(); ?></section>
<section class="rdc-wdg"><h2 class="rdc-wdg__ttl"><?php esc_html_e('Categories','radmanct'); ?></h2><?php $terms=get_terms(array('taxonomy'=>'category','hide_empty'=>true));if(!is_wp_error($terms))\RadmanCT\UI\category_tree($terms,array('current'=>is_category()?get_queried_object_id():0,'ancestors'=>is_category()?get_ancestors(get_queried_object_id(),'category'):array())); ?></section>
<section class="rdc-wdg"><h2 class="rdc-wdg__ttl"><?php esc_html_e('Latest posts','radmanct'); ?></h2><?php foreach(get_posts(array('numberposts'=>5,'post_status'=>'publish','has_password'=>false))as$item)get_template_part('template-parts/blog/preview',null,array('variant'=>'compact','post_id'=>$item->ID)); ?></section>
</div></aside>
