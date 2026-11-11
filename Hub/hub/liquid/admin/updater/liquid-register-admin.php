<?php
/** Local distribution: compatibility view only. No license, domain or updater hooks. */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Liquid_Register {
    public function messages() {
        echo '<h3>' . esc_html__( 'Hub Local', 'hub' ) . '</h3>';
        echo '<p>' . esc_html__( 'This local build does not require theme activation. The original demo and plugin tools remain available.', 'hub' ) . '</p>';
    }

    public function form() {
        // Kept for existing dashboard and Merlin templates. No remote form.
    }
}
