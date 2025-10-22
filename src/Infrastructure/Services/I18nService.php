<?php
/**
 * Internationalization Service
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Infrastructure\Services;

/**
 * Handles plugin internationalization.
 *
 * @since 1.0.0
 */
class I18nService {

	/**
	 * Text domain.
	 *
	 * @var string
	 */
	private $domain;

	/**
	 * Constructor.
	 *
	 * @param string $domain Text domain.
	 */
	public function __construct( string $domain ) {
		$this->domain = $domain;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_text_domain(): void {
		add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( plugin_basename( BONAIRE_ROOT_DIR ) ) . '/languages/'
		);
	}
}
