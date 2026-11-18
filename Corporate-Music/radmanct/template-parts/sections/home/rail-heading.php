<?php defined('ABSPATH')||exit; ?>
<header class="rdc-music-section__hdr">
 <h2 id="<?php echo esc_attr($args['heading_id']); ?>">
  <span class="rdc-music-section__badge" aria-hidden="true"><?php echo isset($args['icon']) ? \RadmanCT\icon($args['icon']) : esc_html($args['badge']); ?></span>
  <?php if(!empty($args['url'])): ?>
   <a href="<?php echo esc_url($args['url']); ?>"><?php echo esc_html($args['title']); ?></a>
  <?php else:echo esc_html($args['title']);endif; ?>
 </h2>
 <?php get_template_part('template-parts/sections/home/rail-arrows',null,$args); ?>
</header>
