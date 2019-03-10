<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the help tab on edit screens for posts, pages and products.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class Bonaire_Contextual_Help {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	/**
	 * The array containing the title and the content of the help tab.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    array $tabs
	 */
	private $tabs;
	
	private function set_tab() {
		
		$this->tabs = array( __( 'HELP', $this->domain ) => array( 'title' => __( 'Bonaire Help', $this->domain ) ) );
	}
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $name The name of this plugin.
	 * @param      string $domain The domain of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
		
		$this->set_tab();
		$this->initialize();
	}
	
	public function add_hooks() {
		
		add_action( 'in_admin_header', array( $this, 'add_bonaire_help_tab' ), 20 );
		
		add_action( 'load-post.php', array( $this, 'add_bonaire_help_tab' ), 10 );
		add_action( 'load-post-new.php', array( $this, 'add_bonaire_help_tab' ), 11 );
		add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'add_bonaire_help_tab' ), 12 );
	}
	
	public function initialize() {
		
		add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'add_bonaire_help_tab' ), 15 );
	}
	
	public function add_bonaire_help_tab() {
		
		foreach ( $this->tabs as $id => $data ) {
			
			$title = __( $data['title'], $this->domain );
			
			get_current_screen()->add_help_tab( array(
				'id' => $id,
				'title' => __( $title, $this->domain ),
				'content' => $this->display_content_callback(),
			) );
		}
	}
	
	/**
	 * Holds the help text content
	 *
	 * @return string $string
	 */
	private function display_content_callback() {
		
		$string = '<p>' . __( "This plugin enables you to have a fullscreen background image with a parallax effect with any image that meets the minimum dimensional requirements ( 1920 x 1200px for vertical parallax, wider for horizontal parallax).", $this->domain ) . '</p>';
		
		$string .= '<p>' . __( "You can choose an overlay image and define it's settings.", $this->domain ) . '</p>';
		
		$string .= '<p>' . __( "The indicated directions are meant to be met while scrolling down the content.", $this->domain ) . '</p>';
		
		$string .= '<p>' . __( "Enjoy!", $this->domain ) . '</p>';
		
		return $string;
	}
	
}
