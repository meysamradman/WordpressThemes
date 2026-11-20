<?php defined('ABSPATH')||exit;require_once get_template_directory().'/inc/pages/service.php';get_header(); ?>
<main id="main-content" class="rdc-inv" tabindex="-1">
 <?php get_template_part('template-parts/layout/page-title'); ?>
 <?php while(have_posts()):the_post(); ?>
  <?php if(post_password_required()): ?>
   <div class="rdc-ctr rdc-main"><?php echo get_the_password_form(); ?></div>
  <?php else: ?>
   <?php if(\RadmanCT\Services\has_portfolio_summary()): ?>
    <?php get_template_part('template-parts/sections/service/intro'); ?>
   <?php endif; ?>
   <?php if(trim(get_the_content())): ?><article class="rdc-ctr rdc-main rdc-cnt"><?php the_content();wp_link_pages(); ?></article><?php endif; ?>
   <?php get_template_part('template-parts/sections/service/mosaic'); ?>
   <?php if(\RadmanCT\Services\has_portfolio_summary()): ?>
    <?php $summary=\RadmanCT\Service\portfolio_summary(); ?>
    <?php get_template_part('template-parts/sections/service/portfolio-summary',null,['summary'=>$summary]); ?>
    <?php get_template_part('template-parts/sections/service/project-list',null,['projects'=>array_slice($summary['projects'],0,5)]); ?>
    <?php get_template_part('template-parts/sections/service/image-strip'); ?>
    <?php get_template_part('template-parts/sections/service/case-study',null,['case_study'=>\RadmanCT\Service\case_study($summary['projects'])]); ?>
    <?php get_template_part('template-parts/sections/service/process'); ?>
   <?php endif; ?>
   <?php get_template_part('template-parts/ui/faq',null,array('items'=>apply_filters('radmanct/services/faq',array()))); ?>
  <?php endif; ?>
 <?php endwhile; ?>
</main>
<?php get_footer(); ?>
