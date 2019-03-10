<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
	include BONAIRE_ROOT_DIR . '../../contact-form-7/includes/contact-form.php';
}
if ( ! class_exists( 'Flamingo_Inbound_Message' ) ) {
	include BONAIRE_ROOT_DIR . '../../flamingo/includes/class-inbound-message.php';
}

/**
 * Gets in touch with Flamingo.
 *
 * @link
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class Bonaire_Adapter extends Flamingo_Inbound_Message {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	protected $domain;
	
	/**
	 * @var \stdClass $stored_options
	 */
	private $stored_options;
	
	/**
	 * @var array $posts
	 */
	private $posts;
	
	public function setUp() {
		
		global $name;
		global $domain;
		global $version;
		
		$this->name = $name;
		$this->domain = $domain;
		$this->version = $version;
		
		require_once BONAIRE_ROOT_DIR . 'admin/class-admin.php';
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
	}
	
	public function __construct( $domain, $stored_options ) {
		
		parent::__construct();
	}
	
	private function post( $post_id ) {
		
		foreach ( $this->posts as $i => $post ) {
			if ( $post_id === $post->id ) {
				return $post;
			}
		}
		
		return false;
	}
	
	private function post_attribute( $post_id, $attribute ) {
		
		foreach ( $this->posts as $i => $post ) {
			
			if ( $post_id === $post->id ) {
				return $post[ $attribute ];
			}
		}
		
		return false;
	}
	
	private function field( $post_id, $field_name ) {
		
		foreach ( $this->posts as $i => $post ) {
			foreach ( $post->fields as $field => $value ) {
				if ( $post_id === $post->id && $field_name === $field ) {
					return $value;
				}
			}
		}
		
		return false;
	}
	
}
