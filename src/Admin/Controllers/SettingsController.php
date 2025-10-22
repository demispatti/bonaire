<?php
/**
 * Settings Controller
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Controllers;

use Bonaire\Admin\Services\OptionsService;
use Bonaire\Admin\Views\SettingsPageView;

/**
 * Handles settings page functionality.
 *
 * @since 1.0.0
 */
class SettingsController {

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
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @return void
	 */
	public function add_settings_page(): void {
		add_options_page(
			__( 'Bonaire Settings', 'bonaire' ),
			__( 'Bonaire', 'bonaire' ),
			'manage_options',
			'bonaire',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$view = new SettingsPageView( $this->options_service );
		$view->render();
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			'bonaire_options_group',
			'bonaire_options',
			[
				'sanitize_callback' => [ $this, 'sanitize_options' ],
			]
		);
	}

	/**
	 * Sanitize and validate options.
	 *
	 * @param array $input Raw input.
	 * @return array Sanitized output.
	 */
	public function sanitize_options( array $input ): array {
		return $this->options_service->validate( $input );
	}
}
