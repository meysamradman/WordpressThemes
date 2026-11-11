<?php

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/wpml/builder.php';
if ( is_admin() ) { require_once __DIR__ . '/wpml/admin-builder.php'; }
