<?php defined('ABSPATH')||exit; ?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('rdc-site'); ?>><?php wp_body_open(); ?><a class="rdc-skip" href="#main-content"><?php esc_html_e('Skip to content','radmanct'); ?></a>
<div id="rdc-scr-wrp"><div id="rdc-scr-cnt"><?php get_template_part('template-parts/layout/header'); ?>
