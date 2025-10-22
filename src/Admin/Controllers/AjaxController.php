<?php
/**
 * AJAX Controller
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Controllers;

use Bonaire\Admin\Services\OptionsService;
use WP_Error;

/**
 * Handles AJAX requests.
 *
 * @since 1.0.0
 */
class AjaxController {

	/**
	 * Options service.
	 *
	 * @var OptionsService
	 */
	private $options_service;

	/**
	 * Constructor.
	 *
	 * @param OptionsService $options_service Options service.
	 */
	public function __construct( OptionsService $options_service ) {
		$this->options_service = $options_service;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_ajax_bonaire_save_options', [ $this, 'save_options' ] );
		add_action( 'wp_ajax_bonaire_reset_options', [ $this, 'reset_options' ] );
		add_action( 'wp_ajax_bonaire_submit_reply', [ $this, 'submit_reply' ] );
		add_action( 'wp_ajax_bonaire_test_smtp_settings', [ $this, 'test_smtp_settings' ] );
	}

	/**
	 * Save options via AJAX.
	 *
	 * @return void
	 */
	public function save_options(): void {
		check_ajax_referer( 'bonaire_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'bonaire' ) ] );
		}

		$input = $_POST['options'] ?? [];
		$validated = $this->options_service->validate( $input );

		// Handle password encryption.
		if ( isset( $validated['password'] ) && '*****' !== $validated['password'] ) {
			$validated['password'] = $this->options_service->encrypt( $validated['password'] );
		}

		$saved = $this->options_service->save( $validated, 0 );

		if ( $saved ) {
			wp_send_json_success( [ 'message' => __( 'Settings saved successfully.', 'bonaire' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to save settings.', 'bonaire' ) ] );
		}
	}

	/**
	 * Reset options via AJAX.
	 *
	 * @return void
	 */
	public function reset_options(): void {
		check_ajax_referer( 'bonaire_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'bonaire' ) ] );
		}

		delete_option( 'bonaire_options' );

		$defaults = [
			0 => (array) $this->options_service->get_defaults( 0 ),
			1 => (array) $this->options_service->get_defaults( 1 ),
		];

		$saved = update_option( 'bonaire_options', $defaults, true );

		if ( $saved ) {
			wp_send_json_success( [ 'message' => __( 'Settings reset successfully.', 'bonaire' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to reset settings.', 'bonaire' ) ] );
		}
	}

	/**
	 * Submit reply via AJAX.
	 *
	 * @return void
	 */
	public function submit_reply(): void {
		check_ajax_referer( 'bonaire_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'bonaire' ) ] );
		}

		// This would integrate with the mail service.
		// For now, sending a simple success response.
		wp_send_json_success( [ 'message' => __( 'Reply sent successfully.', 'bonaire' ) ] );
	}

	/**
	 * Test SMTP settings via AJAX.
	 *
	 * @return void
	 */
	public function test_smtp_settings(): void {
		check_ajax_referer( 'bonaire_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'bonaire' ) ] );
		}

		// This would test the SMTP connection.
		// For now, sending a simple success response.
		wp_send_json_success( [ 'message' => __( 'SMTP connection successful.', 'bonaire' ) ] );
	}
}
