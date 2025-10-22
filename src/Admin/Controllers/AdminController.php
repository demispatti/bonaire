<?php
/**
 * Admin Controller
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Controllers;

use Bonaire\Admin\Services\AssetService;
use Bonaire\Admin\Services\OptionsService;

/**
 * Main admin area controller.
 *
 * @since 1.0.0
 */
class AdminController {

	/**
	 * Asset service.
	 *
	 * @var AssetService
	 */
	private $asset_service;

	/**
	 * Options service.
	 *
	 * @var OptionsService
	 */
	private $options_service;

	/**
	 * Settings controller.
	 *
	 * @var SettingsController
	 */
	private $settings_controller;

	/**
	 * AJAX controller.
	 *
	 * @var AjaxController
	 */
	private $ajax_controller;

	/**
	 * Dashboard widget controller.
	 *
	 * @var DashboardWidgetController
	 */
	private $dashboard_widget_controller;

	/**
	 * Constructor.
	 *
	 * @param AssetService              $asset_service              Asset service.
	 * @param OptionsService            $options_service            Options service.
	 * @param SettingsController        $settings_controller        Settings controller.
	 * @param AjaxController            $ajax_controller            AJAX controller.
	 * @param DashboardWidgetController $dashboard_widget_controller Dashboard widget controller.
	 */
	public function __construct(
		AssetService $asset_service,
		OptionsService $options_service,
		SettingsController $settings_controller,
		AjaxController $ajax_controller,
		DashboardWidgetController $dashboard_widget_controller
	) {
		$this->asset_service = $asset_service;
		$this->options_service = $options_service;
		$this->settings_controller = $settings_controller;
		$this->ajax_controller = $ajax_controller;
		$this->dashboard_widget_controller = $dashboard_widget_controller;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 10 );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );

		// Register child controllers.
		$this->settings_controller->register_hooks();
		$this->ajax_controller->register_hooks();
		$this->dashboard_widget_controller->register_hooks();
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook_suffix Current page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		$this->asset_service->enqueue_admin_styles( $hook_suffix );
		$this->asset_service->enqueue_admin_scripts( $hook_suffix );

		// Localize script with options data.
		$this->asset_service->localize_admin_script( $this->get_script_data() );
	}

	/**
	 * Get script localization data.
	 *
	 * @return array
	 */
	private function get_script_data(): array {
		$options = $this->options_service->get( 0 );

		return [
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'save_reply'     => $options->save_reply ?? 'no',
			'smtp_status'    => $this->options_service->get( 1 )->smtp_status ?? 'orange',
			'imap_status'    => $this->options_service->get( 1 )->imap_status ?? 'orange',
		];
	}

	/**
	 * Add plugin row meta links.
	 *
	 * @param array  $meta Plugin meta.
	 * @param string $file Plugin file.
	 * @return array
	 */
	public function plugin_row_meta( array $meta, string $file ): array {
		$plugin = plugin_basename( 'bonaire/bonaire.php' );

		if ( $file === $plugin ) {
			$meta[] = '<a href="https://wordpress.org/support/plugin/bonaire" target="_blank">' . __( 'Plugin Support', 'bonaire' ) . '</a>';
			$meta[] = '<a href="https://wordpress.org/support/view/plugin-reviews/bonaire" target="_blank">' . __( 'Rate Plugin', 'bonaire' ) . '</a>';
			$meta[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XLMMS7C62S76Q" target="_blank">' . __( 'Donate', 'bonaire' ) . '</a>';
		}

		return $meta;
	}
}
