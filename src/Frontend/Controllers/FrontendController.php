<?php
/**
 * Frontend Controller
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Frontend\Controllers;

/**
 * Handles frontend functionality.
 *
 * @since 1.0.0
 */
class FrontendController {

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		// Hook into Contact Form 7 submission.
		add_filter( 'wpcf7_posted_data', [ $this, 'capture_form_data' ] );
		add_action( 'wpcf7_mail_sent', [ $this, 'after_mail_sent' ] );
	}

	/**
	 * Capture Contact Form 7 posted data.
	 *
	 * @param array $posted_data Form data.
	 * @return array
	 */
	public function capture_form_data( $posted_data ): array {
		// Store in transient for later processing.
		set_transient( 'bonaire_wpcf7_temp_data', $posted_data, 60 );

		return $posted_data;
	}

	/**
	 * After Contact Form 7 mail is sent.
	 *
	 * @param object $contact_form Contact form object.
	 * @return void
	 */
	public function after_mail_sent( $contact_form ): void {
		$posted_data = get_transient( 'bonaire_wpcf7_temp_data' );

		if ( ! $posted_data ) {
			return;
		}

		// Queue for Flamingo processing.
		set_transient( 'bonaire_wpcf7_queue', $posted_data, 300 );

		// Clean up temp data.
		delete_transient( 'bonaire_wpcf7_temp_data' );
	}
}
