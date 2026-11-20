<?php
namespace RadmanLibrary\Designs\PortfolioRows;

defined('ABSPATH') || exit;

function data(): array {
    $projects = get_posts([
        'post_type' => 'project',
        'post_status' => 'publish',
        'has_password' => false,
        'posts_per_page' => 5,
        'no_found_rows' => true,
    ]);
    return apply_filters('rdl/portfolio-rows/projects', $projects);
}
