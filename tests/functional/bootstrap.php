<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Bonaire
 */

define( 'BONAIRE_TESTS', '1' );

if ( ! defined( 'BONAIRE_ROOT_DIR' ) ) {
	define( 'BONAIRE_ROOT_DIR', dirname( __FILE__ ) . '/../../src/' );
}

if ( ! defined( 'BONAIRE_ROOT_URL' ) ) {
	$a = dirname( __FILE__ ) . '/../../src/';
	define( 'BONAIRE_ROOT_URL', dirname( __FILE__ ) . '/../../src/' );
}

if ( ! defined( 'WP_ROOT_PATH' ) ) {
	define( 'WP_ROOT_PATH', dirname( __FILE__ ) . '/../../../../../' );
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
include $_tests_dir . '/includes/functions.php';

// Manually load the plugins this plugin depends on .
tests_add_filter( 'muplugins_loaded', '_load_plugin_prerequisites', 20 );
function _load_plugin_prerequisites() {
	
	include BONAIRE_ROOT_DIR . '../../contact-form-7/wp-contact-form-7.php';
	include BONAIRE_ROOT_DIR . '../../flamingo/flamingo.php';
}

// Manually load the plugin.
tests_add_filter( 'muplugins_loaded', '_load_plugin', 30 );
function _load_plugin() {
	
	include BONAIRE_ROOT_DIR . 'bonaire.php';
}

// Start up the WP testing environment.
include $_tests_dir . '/includes/bootstrap.php';
