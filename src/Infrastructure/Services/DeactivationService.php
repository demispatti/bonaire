<?php
/**
 * Plugin Deactivation Service
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Infrastructure\Services;

/**
 * Handles plugin deactivation.
 *
 * @since 1.0.0
 */
class DeactivationService {

	/**
	 * Deactivate the plugin.
	 *
	 * @return void
	 */
	public function deactivate(): void {
		// Flush rewrite rules.
		flush_rewrite_rules();

		// Clear any transients.
		$this->clear_transients();

		// Log deactivation if needed.
		do_action( 'bonaire_deactivated' );
	}

	/**
	 * Clear plugin transients.
	 *
	 * @return void
	 */
	private function clear_transients(): void {
		delete_transient( 'bonaire_wpcf7_queue' );
	}
}
