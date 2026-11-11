<?php

/**
 * The Liquid Themes Hub Theme
 *
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Text Domain: 'hub'
 * Domain Path: /languages/
 */
// This local build has no theme activation or license gate.
// Starting The Engine / Load the Liquid Framework ----------------
include_once( get_template_directory() . '/liquid/liquid-init.php' );
// Keep vendor updates available without activation or domain locks.
require_once get_template_directory() . '/liquid/admin/updater/liquid-updater-class.php';
new Liquid_Updater();
