<?php

namespace RadmanCT\Builder;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/builder/storage.php';
require_once __DIR__ . '/builder/layout.php';
require_once __DIR__ . '/builder/responsive.php';
require_once __DIR__ . '/builder/appearance.php';
require_once __DIR__ . '/builder/header-colors.php';
require_once __DIR__ . '/builder/schema.php';
require_once __DIR__ . '/builder/tree.php';
require_once __DIR__ . '/builder/selection.php';
require_once __DIR__ . '/builder/frontend.php';

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue', 20 );
