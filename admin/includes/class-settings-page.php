<?php
namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Partials as AdminPartials;
use Bonaire\Admin\Includes as AdminIncludes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'AdminPartials\Bonaire_Settings_Page_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/partials/class-settings-page-display.php';
}

/**
 * The class responsible for creating and displaying the settings page.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Settings_Page {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * The instance that's responsible for displaying the settings page content.
	 *
	 * @var AdminPartials\Bonaire_Settings_Page_Display $Bonaire_Settings_Page_Display
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Settings_Page_Display;
	
	/**
	 * Sets the instance responsible for displaying the settings page.
	 *
	 * @param $Bonaire_Options
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function set_settings_page_display_instance( $Bonaire_Options ) {
		
		$this->Bonaire_Settings_Page_Display = new AdminPartials\Bonaire_Settings_Page_Display( $this->domain, $Bonaire_Options );
	}
	
	/**
	 * Bonaire_Settings_Page constructor.
	 *
	 * @param string $domain The domain of this plugin.
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain, $Bonaire_Options ) {
		
		$this->domain = $domain;
		$this->set_settings_page_display_instance( $Bonaire_Options );
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ), 10 );
	}
	
	/**
	 * Localizes the admin javascript file.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function localize_script() {
		
		$notifications = $this->get_settings_page_notifications();
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireOptionsPage', $notifications );
	}
	
	/**
	 * Returns an array containing the notifications used for interactions in the plugin settings page.
	 * This array gets sent to the admin javasctipt file.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function get_settings_page_notifications() {
		
		return array(
			'settings_page_notifications' => array(
				'save_options_title' => __( 'Save Options', $this->domain ),
				'save_options_notice' => __( 'Nothing to save.', $this->domain ),
				'reset_options_title' => __( 'Reset Options', $this->domain ),
				'reset_options_notice' => __( 'Nothing to reset.', $this->domain ),
				'send_test_mail_title' => __( 'Send Test Mail.', $this->domain ),
				'send_test_mail_notice' => __( 'Please fill in your email account details, then test the Form Tags, the SMTP and IMAP Settings, and try again.', $this->domain ),
				'send_test_mail_prompt_title' => __( 'Send Test Message', $this->domain ),
				'send_test_mail_prompt_review_email_title' => __( 'Please correct the email address', $this->domain ),
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
				'title' => __( 'Please fill in your email account details, then test the Form Tags, the SMTP and IMAP Settings, and try again.', $this->domain ),
				'text' => __( 'Go to settings page:', $this->domain ),
				'link' => '/wp-admin/options-general.php?page=bonaire.php',
				'link_text' => __( 'Go!', $this->domain )
			),
			'empty_message_error' => array(
				'title' => __( 'Attention', $this->domain ),
				'text' => __( 'Please type your message first.', $this->domain )
			),
			'reset_options_confirmation' => array(
				'title' => __( 'Reset Options', $this->domain ),
				'text' => __( 'Are you sure?', $this->domain )
			),
		);
	}
	
	/**
	 * Registers the settings page with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_settings_page() {
		
		add_options_page( __( 'Bonaire Settings Page', $this->domain ),
			'Bonaire',
			'manage_options',
			'bonaire.php',
			array( $this, 'settings_page_display' )
		);
	}
	
	/**
	 * Echoes a string containing the settings page content.
	 *
	 * @since 0.9.6
	 * @echo  string
	 */
	public function settings_page_display() {
		
		echo $this->Bonaire_Settings_Page_Display->settings_page_display();
	}
	
}
