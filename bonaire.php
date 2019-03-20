<?php

/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link
 * @since             1.0.0
 * @package           Bonaire
 * @wordpress-plugin
 * Plugin Name:       Bonaire
 * Plugin URI:
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Demis Patti
 * Author URI:
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bonaire
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include the plugin.
require plugin_dir_path( __FILE__ ) . 'src/bonaire.php';
