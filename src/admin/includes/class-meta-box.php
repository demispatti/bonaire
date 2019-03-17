<?php

namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Includes as AdminIncludes;
use Bonaire\Admin\Partials as AdminPartials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'AdminPartials\Bonaire_Reply_Form_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . '/admin/partials/class-reply-form-display.php';
}

/**
 * The class responsible for creating and displaying the meta box containing the reply form.
 *
 * @since            0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Meta_Box {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since   0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the instance that's responsible for displaying the reply form.
	 *
	 * @var AdminPartials\Bonaire_Reply_Form_Display $Bonaire_Reply_Form_Display
	 * @since   0.9.6
	 * @access   private
	 */
	private $Bonaire_Reply_Form_Display;
	
	/**
	 * Holds the instance that's responsible for connecting to Contact Form 7 and Flamingo.
	 *
	 * @var AdminIncludes\Bonaire_Adapter Bonaire_Adapter
	 * @since   0.9.6
	 * @access   private
	 */
	private $Bonaire_Adapter;
	
	/**
	 * Holds the instance that's responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since   0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Sets the instance responsible for displaying the reply form.
	 *
	 * @since0.9.6
	 * @return void
	 */
	private function set_reply_form_display_instance() {
		
		$this->Bonaire_Reply_Form_Display = new AdminPartials\Bonaire_Reply_Form_Display( $this->domain );
	}
	
	/**
	 * Bonaire_Meta_Box constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Adapter $Bonaire_Adapter
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 *
	 * @since0.9.6
	 * @return void
	 */
	public function __construct( $domain, $Bonaire_Adapter, $Bonaire_Options ) {
		
		$this->domain = $domain;
		$this->Bonaire_Adapter = $Bonaire_Adapter;
		$this->Bonaire_Options = $Bonaire_Options;
		
		$this->set_reply_form_display_instance();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @since0.9.6
	 * @return void
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'load-flamingo_page_flamingo_inbound', array( $this, 'add_meta_box' ) );
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since0.9.6
	 * @return void
	 */
	public function enqueue_styles() {
		
		// Media Frame.
		wp_enqueue_script( 'dashicons' );
	}
	
	/**
	 * Registers the JavaScript for the admin area.
	 *
	 * @since0.9.6
	 * @return void
	 */
	public function enqueue_scripts() {
		
		// Media Frame.
		wp_enqueue_script( 'media-views' );
		
		// Media upload engine.
		wp_enqueue_media();
	}
	
	/**
	 * Registers the meta box with WordPress.
	 *
	 * @since0.9.6
	 * @return void
	 */
	public function add_meta_box() {
		
		add_meta_box(
			'bonaire-form-meta-box',
			__( 'Reply', $this->domain ),
			array( $this, 'display_reply_form_meta_box' ),
			'flamingo_page_flamingo_inbound'
		);
	}
	
	/**
	 * Creates and displays the meta box containing the reply form.
	 *
	 * @since0.9.6
	 * @echo string $string
	 */
	public function display_reply_form_meta_box() {
		
		$post_id = (int) $_REQUEST['post'];
		
		if ( true !== $this->Bonaire_Options->get_settings_state( 'smtp' ) ) {
			$recipient_email_address = $this->Bonaire_Adapter->get_recipient_email_address( $post_id );
			$url = site_url() . '/wp-admin/options-general.php?page=bonaire.php';
			$link = '<a href="' . $url . '">' . __( 'Account Settings', $this->domain ) . '</a>';
			
			echo __( 'Please register the email account that is related to the following email account in order to send replies' ) . ': (' . $recipient_email_address . '). ' . $link;
			
			return;
		}
		
		$your_subject = $this->Bonaire_Adapter->get_field( $post_id, 'your-subject' );
		$string = AdminPartials\Bonaire_Reply_Form_Display::reply_form_display( $your_subject, $this->Bonaire_Options->get_stored_options( 0 ) );
		
		echo $string;
	}
	
}
