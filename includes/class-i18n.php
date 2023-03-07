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
 * @since      0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/includes
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_i18n {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Bonaire_i18n constructor.
	 *
	 * @param string $domain
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}
	
	/**
	 * Loads the plugin text domain for translation.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function load_plugin_textdomain() {
		
		load_plugin_textdomain( $this->domain, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}
	
}
