<?php defined('ABSPATH') || exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="rds-skip" href="#main-content"><?php esc_html_e('رفتن به محتوا','radmanstore'); ?></a>
<header class="rds-header"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
<nav aria-label="<?php esc_attr_e('فهرست اصلی','radmanstore'); ?>"><?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false]); ?></nav></header>
