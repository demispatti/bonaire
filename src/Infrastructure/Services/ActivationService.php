<?php
/**
 * Plugin Activation Service
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Infrastructure\Services;

/**
 * Handles plugin activation.
 *
 * @since 1.0.0
 */
class ActivationService {

	/**
	 * Activate the plugin.
	 *
	 * @return void
	 */
	public function activate(): void {
		// Flush rewrite rules.
		flush_rewrite_rules();

		// Set default options if they don't exist.
		$this->set_default_options();

		// Log activation if needed.
		do_action( 'bonaire_activated' );
	}

	/**
	 * Set default plugin options.
	 *
	 * @return void
	 */
	private function set_default_options(): void {
		$default_options = [
			0 => [
				'channel'                       => '',
				'username'                      => '',
				'password'                      => '',
				'smtp_host'                     => '',
				'smtp_port'                     => 465,
				'smtpsecure'                    => 'ssl',
				'fromname'                      => '',
				'from'                          => '',
				'save_reply'                    => 'no',
				'imapsecure'                    => 'ssl',
				'imap_host'                     => '',
				'imap_port'                     => 993,
				'inbox_folder_name'             => 'Sent',
				'inbox_folder_path'             => '',
				'ssl_certification_validation'  => 'cert',
			],
			1 => [
				'smtp_status' => 'orange',
				'imap_status' => 'inactive',
			],
		];

		if ( false === get_option( 'bonaire_options' ) ) {
			add_option( 'bonaire_options', $default_options );
		}
	}
}
