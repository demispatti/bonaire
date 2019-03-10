<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bonaire_Settings_Page {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	/**
	 * @var Bonaire_Settings_Page_Display $Bonaire_Settings_Page_Display
	 */
	private $Bonaire_Settings_Page_Display;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $name The name of this plugin.
	 * @param      string $domain The domain of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $domain, $Bonaire_Settings_Page_Display ) {
		
		$this->Bonaire_Settings_Page_Display = $Bonaire_Settings_Page_Display;
		$this->domain = $domain;
	}
	
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ), 10 );
	}
	
	public function localize_script() {
		
		$notifications = $this->get_settings_page_notifications();
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireOptionsPage', $notifications );
	}
	
	/**
	 * Holds the notifications used for interactions in the plugin settings page
	 *
	 * @return array
	 */
	private function get_settings_page_notifications() {
		
		return array(
			'settings_page_notifications' => array(
				'save_options_title' => __( 'Save Options', $this->domain ),
				'save_options_notice' => __( 'Nothing to save.', $this->domain ),
				'reset_options_title' => __( 'Reset Options', $this->domain ),
				'reset_options_notice' => __( 'Nothing to reset.', $this->domain ),
				'send_test_mail_title' => __( 'Please fill in your details first.', $this->domain ),
				'send_test_mail_notice' => __( 'Please fill in your details first.', $this->domain ),
				'working' => __( 'working', $this->domain ),
			),
			'alertify_notifications' => array(
				'ok' => __( 'ok', $this->domain ),
				'cancel' => __( 'cancel', $this->domain )
			),
			'alertify_error' => array(
				'title' => __( 'Please fix following errors:', $this->domain )
			),
			'reply_error' => array(
				'title' => __( 'Please fill in your details first.', $this->domain ),
				'text' => __( 'Go to settings page:', $this->domain ),
				'link' => '/wp-admin/options-general.php?page=bonaire.php',
				'link_text' => __( 'Go!', $this->domain )
			),
			'reset_options_confirmation' => array(
				'title' => __( 'Reset Options', $this->domain ),
				'text' => __( 'Are you sure?', $this->domain )
			),
		);
	}
	
	public function add_settings_page() {
		
		add_options_page( 'Bonaire Settings Page',
			'Bonaire Settings',
			'manage_options',
			'bonaire.php',
			array( $this, 'settings_page_display' )
		);
	}
	
	public function settings_page_display() {
		
		echo $this->Bonaire_Settings_Page_Display->get_form();
	}
	
}
