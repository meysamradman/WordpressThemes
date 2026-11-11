<?php

namespace RadmanCTComponents\Integrations\Elementor\Atomic;

defined( 'ABSPATH' ) || exit;

function enabled(): bool {
	return class_exists( '\\Elementor\\Modules\\AtomicWidgets\\Module' ) && \Elementor\Modules\AtomicWidgets\Module::is_active();
}
