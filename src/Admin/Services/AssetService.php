<?php
/**
 * Asset Service
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Services;

/**
 * Handles asset loading (CSS/JS).
 *
 * @since 1.0.0
 */
class AssetService {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Plugin root URL.
	 *
	 * @var string
	 */
	private $root_url;

	/**
	 * Constructor.
	 *
	 * @param string $version  Plugin version.
	 * @param string $root_url Plugin root URL.
	 */
	public function __construct( string $version, string $root_url ) {
		$this->version = $version;
		$this->root_url = rtrim( $root_url, '/' );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_styles( string $hook_suffix ): void {
		$allowed_pages = $this->get_allowed_pages();

		if ( ! in_array( $hook_suffix, $allowed_pages, true ) ) {
			return;
		}

		// WordPress core styles.
		wp_enqueue_style( 'dashicons' );

		// Third-party styles (from node_modules via webpack).
		wp_enqueue_style(
			'bonaire-admin',
			$this->root_url . '/assets/dist/css/admin-styles.css',
			[],
			$this->version,
			'all'
		);
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_scripts( string $hook_suffix ): void {
		$allowed_pages = $this->get_allowed_pages();

		if ( ! in_array( $hook_suffix, $allowed_pages, true ) ) {
			return;
		}

		// WordPress core scripts.
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-effects-core' );

		// Admin script (bundled with webpack).
		wp_enqueue_script(
			'bonaire-admin',
			$this->root_url . '/assets/dist/js/admin.js',
			[ 'jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-effects-core' ],
			$this->version,
			true
		);

		// Tooltips script.
		wp_enqueue_script(
			'bonaire-tooltips',
			$this->root_url . '/assets/dist/js/tooltips.js',
			[ 'jquery' ],
			$this->version,
			true
		);
	}

	/**
	 * Localize admin script with data.
	 *
	 * @param array $data Data to localize.
	 * @return void
	 */
	public function localize_admin_script( array $data ): void {
		wp_localize_script( 'bonaire-admin', 'BonaireOptions', $data );
	}

	/**
	 * Get allowed admin pages for asset loading.
	 *
	 * @return array
	 */
	private function get_allowed_pages(): array {
		return [
			'settings_page_bonaire',
			'flamingo_page_flamingo_inbound',
			'index.php',
			'admin-ajax.php',
		];
	}
}
