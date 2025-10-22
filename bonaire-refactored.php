<?php
/**
 * Bonaire
 *
 * @package           Bonaire
 * @author            Demis Patti
 * @copyright         2024 Demis Patti
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Bonaire
 * Plugin URI:        https://demispatti.ch
 * Description:       Send email replies to messages received through Contact Form 7 and stored in Flamingo.
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * Author:            Demis Patti
 * Author URI:        https://demispatti.ch
 * Text Domain:       bonaire
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Bonaire;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin constants.
 */
define( 'BONAIRE_VERSION', '1.0.0' );
define( 'BONAIRE_ROOT_DIR', plugin_dir_path( __FILE__ ) );
define( 'BONAIRE_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'BONAIRE_BASENAME', plugin_basename( __FILE__ ) );

// Check if running in development mode.
if ( ! defined( 'BONAIRE_SCRIPT_DEBUG' ) ) {
	define( 'BONAIRE_SCRIPT_DEBUG', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
}

/**
 * Autoloader via Composer.
 */
require_once BONAIRE_ROOT_DIR . 'vendor/autoload.php';

/**
 * Plugin activation hook.
 */
register_activation_hook(
	__FILE__,
	function () {
		$activator = new Infrastructure\Services\ActivationService();
		$activator->activate();
	}
);

/**
 * Plugin deactivation hook.
 */
register_deactivation_hook(
	__FILE__,
	function () {
		$deactivator = new Infrastructure\Services\DeactivationService();
		$deactivator->deactivate();
	}
);

/**
 * Initialize the plugin.
 */
add_action(
	'plugins_loaded',
	function () {
		// Check for required plugins.
		if ( ! class_exists( 'WPCF7' ) || ! class_exists( 'Flamingo_Admin' ) ) {
			add_action(
				'admin_notices',
				function () {
					?>
					<div class="notice notice-error">
						<p>
							<?php
							esc_html_e(
								'Bonaire requires Contact Form 7 and Flamingo to be installed and activated.',
								'bonaire'
							);
							?>
						</p>
					</div>
					<?php
				}
			);
			return;
		}

		// Initialize plugin.
		$plugin = new Core\Plugin( 'bonaire', 'bonaire', BONAIRE_VERSION );
		$plugin->initialize();

		// Store plugin instance globally for access.
		$GLOBALS['bonaire'] = $plugin;
	},
	10
);

/**
 * Helper function to get plugin instance.
 *
 * @return Core\Plugin|null
 */
function bonaire() {
	return $GLOBALS['bonaire'] ?? null;
}
