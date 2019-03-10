<?php

namespace Bonaire\Includes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for internationalizing functionality.
 *
 * @since      1.0.0
 * @package    Bonaire
 * @subpackage Bonaire/includes
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_i18n {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    1.0.0
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Bonaire_i18n constructor.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_hooks() {
		
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}
	
	/**
	 * Loads the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_plugin_textdomain() {
		
		load_plugin_textdomain( $this->domain, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}
	
}
