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
if ( ! class_exists( 'AdminPartials\Bonaire_Message_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/partials/class-message-display.php';
}
if ( ! class_exists( 'AdminPartials\Bonaire_Reply_Form_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/partials/class-reply-form-display.php';
}

/**
 * The class responsible for creating and displaying the meta box containing the reply form.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Meta_Box {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the instance that's responsible for displaying the reply form.
	 *
	 * @var AdminPartials\Bonaire_Reply_Form_Display $Bonaire_Reply_Form_Display
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Reply_Form_Display;
	
	/**
	 * Holds the instance that's responsible for connecting to Contact Form 7 and Flamingo.
	 *
	 * @var AdminIncludes\Bonaire_Adapter Bonaire_Adapter
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Adapter;
	
	/**
	 * Holds the instance that's responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Sets the instance responsible for displaying the reply form.
	 *
	 * @return void
	 * @since 0.9.6
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
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain, $Bonaire_Adapter, $Bonaire_Options ) {
		
		$this->domain          = $domain;
		$this->Bonaire_Adapter = $Bonaire_Adapter;
		$this->Bonaire_Options = $Bonaire_Options;
		
		$this->set_reply_form_display_instance();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
		
		// Show this meta box anyway since it hosts some user messages [for now] in case the plugin isn\'t configured propperly.
		add_action( 'load-flamingo_page_flamingo_inbound', array( $this, 'add_reply_form_meta_box' ), 11 );
		
		// Show this meta box only if the Bonaire is configured propperly and the user can indeed respond to this message.
		if(false === $this->can_reply()){
			return;
		}
		add_action( 'load-flamingo_page_flamingo_inbound', array( $this, 'add_message_meta_box' ), 10 );
	}
	
	private function can_reply() {
		
		$post_id                         = (int) $_REQUEST['post'];
		$stored_options                  = $this->Bonaire_Options->get_stored_options();
		$Bonaire_Account_Settings_Status = new Bonaire_Settings_Status( $this->domain );
		$smtp_status                     = $Bonaire_Account_Settings_Status->get_settings_status( 'smtp', true );
		$imap_status                     = $Bonaire_Account_Settings_Status->get_settings_status( 'imap', true );
		$save_reply                      = isset( $stored_options->save_reply ) ? $stored_options->save_reply : 'no';
		$recipient_email_address         = $this->Bonaire_Adapter->get_recipient_email_address( $post_id );
		$uniqid                          = $this->Bonaire_Adapter->get_meta_field( $post_id, 'posted_data_uniqid' );
		
		if ( ( false === $uniqid || false === $recipient_email_address ) ||
		     false === $this->Bonaire_Adapter->is_same_email_address( $post_id ) ||
		     ( 'yes' === $save_reply && false === $imap_status || 'no' === $save_reply && false === $smtp_status ) ) {
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function enqueue_styles() {
		
		// Media Frame.
		wp_enqueue_script( 'dashicons' );
	}
	
	public function add_message_meta_box() {
		
		add_meta_box(
			'bonaire-message-meta-box',
			__( 'Message', $this->domain ),
			array( $this, 'display_message_meta_box' ),
			'flamingo_page_flamingo_inbound'
		);
	}
	
	/**
	 * Registers the meta box with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_reply_form_meta_box() {
		
		add_meta_box(
			'bonaire-form-meta-box',
			__( 'Reply', $this->domain ),
			array( $this, 'display_reply_form_meta_box' ),
			'flamingo_page_flamingo_inbound'
		);
	}
	
	public function localize_script() {
		
		$handle_divs = $this->can_reply() ? '1' : '0';
		wp_localize_script( 'bonaire-admin-js', 'BonaireOptions', array( 'manage_handle_divs' => $handle_divs ) );
	}
	
	/**
	 * Creates and displays the meta box containing the reply form.
	 *
	 * @since 0.9.6
	 * @echo  string $string
	 */
	public function display_message_meta_box() {
		
		$post_id = (int) isset($_REQUEST['post']) && is_int( (int)$_REQUEST['post']) ? $_REQUEST['post'] : false;
		if(false === $post_id){
			return;
		}

		$post_meta      = get_post_meta( $post_id );
		$your_message   = isset($post_meta['_field_your-message'][0]) ? $post_meta['_field_your-message'][0] : __('*no content*', $this->domain);

		/**
		 * Display reply form.
		 */
		$string = AdminPartials\Bonaire_Message_Display::message_display( $your_message );
		echo $string;
		
		return;
	}
	
	/**
	 * Creates and displays the meta box containing the message text.
	 *
	 * @since 1.0.0
	 * @echo  string $string
	 */
	public function display_reply_form_meta_box() {
		
		$post_id = isset( $_REQUEST['post'] ) && is_int( (int) $_REQUEST['post'] ) ? (int) $_REQUEST['post'] : false;
		if ( false === $post_id ) {
			return;
		}
		
		$stored_options                  = $this->Bonaire_Options->get_stored_options();
		$Bonaire_Account_Settings_Status = new Bonaire_Settings_Status( $this->domain );
		$cf7_status                      = $Bonaire_Account_Settings_Status->get_settings_status( 'cf7', true );
		$smtp_status                     = $Bonaire_Account_Settings_Status->get_settings_status( 'smtp', true );
		$imap_status                     = $Bonaire_Account_Settings_Status->get_settings_status( 'imap', true );
		$save_reply                      = isset( $stored_options->save_reply ) ? $stored_options->save_reply : 'no';
		$recipient_email_address         = $this->Bonaire_Adapter->get_recipient_email_address( $post_id );
		$settings_page_link              = '<a href="' . esc_url( site_url() . '/wp-admin/options-general.php?page=bonaire.php' ) . '" target="_blank">' . __( 'Plugin Settings Page', $this->domain ) . '</a>';
		$contactforms_page_link          = '<a href="' . esc_url( site_url() . '/wp-admin/admin.php?page=wpcf7' ) . '" target="_blank">' . __( 'Contact Forms Page', $this->domain ) . '</a>';
		$uniqid                          = $this->Bonaire_Adapter->get_meta_field( $post_id, 'posted_data_uniqid' );
		
		/**
		 * Check if the message was preprocessed.
		 * If not, a reply is not possible since we don't know the sender's email address.
		 */
		if ( false === $uniqid || false === $recipient_email_address ) {
			echo __('Please Note: This function is available to you for messages you received <i>after</i> installation and configuration of Bonaire Plugin with the respective contact form.', $this->domain );
			
			return;
		}
		
		/**
		 * Check if the necessary account settings are marked as valid.
		 */
		if ( 'yes' === $save_reply && false === $imap_status || 'no' === $save_reply && false === $smtp_status || false === $cf7_status ) {
			printf( __( 'There seems to be a problem with your email settings (%s) or the contact form (%s).', $this->domain ), $settings_page_link, $contactforms_page_link );

			return;
		}
		
		/**
		 * Display reply form.
		 */
		$your_subject = $this->Bonaire_Adapter->get_post_field( $post_id, 'your-subject' );
		$your_email   = $this->Bonaire_Adapter->get_meta_field( $post_id, 'post_author_email' );
		$string       = AdminPartials\Bonaire_Reply_Form_Display::reply_form_display( $your_subject, $your_email, $this->Bonaire_Options->get_stored_options() );
		echo $string;
		
		return;
	}
	
}
