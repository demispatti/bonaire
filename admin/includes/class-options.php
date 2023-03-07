<?php
namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Includes as AdminIncludes;
use WP_Error;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for handling the user options.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
final class Bonaire_Options {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the default options.
	 *
	 * @var      object $default_options
	 * @since    0.9.6
	 * @access   public
	 */
	public $default_options;
	
	/**
	 * Holds the stored options.
	 *
	 * @var      object $stored_options
	 * @since    0.9.6
	 * @access   private
	 */
	public $stored_options;
	
	/**
	 * Holds the account settings part of the stored options.
	 *
	 * @var      object $account_settings
	 * @since    0.9.6
	 * @access   private
	 */
	private $account_settings;
	
	/**
	 * Holds the options meta data.
	 *
	 * @var      object $options_meta
	 * @since    0.9.6
	 * @access   private
	 */
	private $options_meta;
	
	/**
	 * The the option keys that are used to create
	 * the SMTP hash key.
	 *
	 * @var      array $smtp_hash_keys
	 * @since    0.9.6
	 * @access   public
	 */
	public $smtp_hash_keys = array(
		'username' => '',
		'password' => '',
		'smtp_host' => '',
		'smtp_port' => '',
		'smtpsecure' => '',
		'fromname' => '',
		'from' => ''
	);
	
	/**
	 * The option keys that are used to create
	 * the IMAP hash key.
	 *
	 * @var      array $imap_hash_keys
	 * @since    0.9.6
	 * @access   public
	 */
	public $imap_hash_keys = array(
		'username' => '',
		'password' => '',
		'smtp_host' => '',
		'smtp_port' => '',
		'smtpsecure' => '',
		'fromname' => '',
		'from' => '',
		'imapsecure' => '',
		'imap_host' => '',
		'imap_port' => '',
		'inbox_folder_name' => '',
		'inbox_folder_path' => '',
		'ssl_certification_validation' => '',
	);
	
	public $contact_form_hash_keys = array(
		'channel' => ''
	);
	
	/**
	 * Returns the default options.
	 *
	 * @return object $options
	 * @since 0.9.6
	 */
	private function default_options() {
		
		$options      = (object) array();
		$options->{0} = (object) array(
			'channel' => '',
			'username' => '',
			'password' => '',
			'smtp_host' => '',
			'smtp_port' => 465,
			'smtpsecure' => 'ssl',
			'fromname' => '',
			'from' => '',
			'save_reply' => 'no',
			'imapsecure' => 'ssl',
			'imap_host' => '',
			'imap_port' => 993,
			'inbox_folder_name' => 'Sent',
			'inbox_folder_path' => '',
			'ssl_certification_validation' => 'cert',
		);
		$options->{1} = (object) array(
			'smtp_status' => 'orange',
			'imap_status' => 'inactive'
		);
		
		return $options;
	}
	
	/**
	 * Returns the stored options or the default options as a fallback.
	 *
	 * @return object $options
	 * @since 0.9.6
	 */
	private function stored_options() {
		
		$stored_options = get_option( 'bonaire_options' );
		
		if ( false === $stored_options || ! isset( $stored_options[1] ) ) {
			
			return $this->default_options;
		}
		
		$options      = (object) array();
		$options->{0} = (object) $stored_options[0];
		$options->{1} = (object) $stored_options[1];
		
		return $options;
	}
	
	/**
	 * Returns the email account settings or the default settings if none were saved yet.
	 *
	 * @param object $stored_options
	 *
	 * @return object
	 * @since 0.9.6
	 */
	private function account_settings( $stored_options ) {
		
		$account_settings = isset( $stored_options->{0} ) ? $stored_options->{0} : new stdClass();
		
		/**
		 * @var object $account_settings
		 */
		if ( ! empty( $account_settings ) ) {
			
			return $account_settings;
		}
		
		return $this->default_options->{0};
	}
	
	/**
	 * Returns the options meta data.
	 *
	 * @return object $options_meta
	 * @since 0.9.6
	 */
	private function options_meta() {
		
		return (object) array(
			'form_id' => array(
				'id' => 'form_id',
				'name' => __( 'Form Id', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'none',
				'default_value' => '',
				'example' => '2943',
				'tt_image' => '',
				'tt_description' => 'The contact form id.'
			),
			'channel' => array(
				'id' => 'channel',
				'name' => __( 'Contact Form', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'none',
				'default_value' => __( 'none', $this->domain ),
				'example' => __( 'Contact form 1', $this->domain ),
				'values' => array( 'none' => __( 'none', $this->domain ) ),
				'tt_image' => BONAIRE_ROOT_URL . 'admin/images/tooltips/tt-channel.jpg',
				'tt_description' => __( 'The Title of the contactform you want to use this plugin with. Usually it is the form that\'s displayed on the contact page of your website.', $this->domain )
			),
			'number_posts' => array(
				'id' => 'number_posts',
				'name' => __( 'Number Of Messages', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'none',
				'default_value' => '5',
				'example' => __( '5', $this->domain ),
				'values' => array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ),
				'tt_image' => ''/*BONAIRE_ROOT_URL . 'admin/images/tooltips/tt-channel.jpg'*/,
				'tt_description' => __( 'Max. number of unread messages to display on the dashboard widget.', $this->domain )
			),
			'username' => array(
				'id' => 'username',
				'name' => __( 'Username', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => 'yourname@gmail.com',
				'tt_image' => BONAIRE_ROOT_URL . 'admin/images/tooltips/tt-your-email.jpg',
				'tt_description' => __( 'The username you authenticate to the email account with. This is most likely the email address or your name.', $this->domain )
			),
			'password' => array(
				'id' => 'password',
				'name' => __( 'Password', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => __( 'No example', $this->domain ),
				'tt_image' => '',
				'tt_description' => __( 'Your password. It will be stored encrypted in the database, and replaced by ***** in the user interface after saving it. Please make sure you have generated your SALT-Keys.', $this->domain )
			),
			'smtpauth' => array(
				'id' => 'smtpauth',
				'name' => __( 'SMTPAuth', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'general',
				'default_value' => 'true',
				'example' => __( 'No example', $this->domain ),
				'tt_image' => '',
				'tt_description' => __( 'You must authenticate with your username and password.', $this->domain )
			),
			'smtp_host' => array(
				'id' => 'smtp_host',
				'name' => __( 'SMTP Host', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => 'smtp.gmail.com',
				'tt_image' => '',
				'tt_description' => __( 'The address of the SMTP host.', $this->domain ) . '<br>' . __( 'Get this from your hosting or email provider.', $this->domain )
			),
			'smtp_port' => array(
				'id' => 'smtp_port',
				'name' => __( 'SMTP Port', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => '465 for SSL or 587 for TLS',
				'tt_image' => '',
				'tt_description' => __( 'The SMTP port number.', $this->domain ) . '<br>' . __( 'Get this from your hosting or email provider.', $this->domain )
			),
			'smtpsecure' => array(
				'id' => 'smtpsecure',
				'name' => __( 'SMTPSecure', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => 'SSL',
				'example' => 'SSL',
				'values' => array( 'tls' => 'TLS', 'ssl' => 'SSL' ),
				'tt_image' => '',
				'tt_description' => __( 'SSL or TLS is required.', $this->domain ) . '<br>' . __( 'Get this from your hosting or email provider.', $this->domain )
			),
			'fromname' => array(
				'id' => 'fromname',
				'name' => __( 'From Name', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'account',
				'default_value' => '',
				'example' => 'Your Name',
				'tt_image' => '',
				'tt_description' => __( 'Your name or website name or company name or...', $this->domain )
			),
			'from' => array(
				'id' => 'from',
				'name' => __( 'From E-Mail Address', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'account',
				'default_value' => '',
				'example' => 'yourname@gmail.com',
				'tt_image' => BONAIRE_ROOT_URL . 'admin/images/tooltips/tt-your-email.jpg',
				'tt_description' => __( 'The account\'s email address.', $this->domain )
			),
			'save_reply' => array(
				'id' => 'save_reply',
				'name' => __( 'Save Reply', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'none',
				'default_value' => __( 'No', $this->domain ),
				'example' => __( 'Yes', $this->domain ),
				'values' => array( 'no' => __( 'No', $this->domain ), 'yes' => __( 'Yes', $this->domain ) ),
				'tt_image' => '',
				'tt_description' => __( 'Store replies on your mail server inside the default folder for sent items.', $this->domain )
			),
			'imapsecure' => array(
				'id' => 'imapsecure',
				'name' => __( 'IMAPSecure', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'imap',
				'default_value' => 'SSL',
				'example' => 'SSL',
				'values' => array( 'tls' => 'TLS', 'ssl' => 'SSL' ),
				'tt_image' => '',
				'tt_description' => __( 'SSL or TLS is required.', $this->domain )
			),
			'imap_host' => array(
				'id' => 'imap_host',
				'name' => __( 'IMAP Host', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'imap',
				'default_value' => '',
				'example' => 'imap.gmail.com',
				'tt_image' => '',
				'tt_description' => __( 'The address of the IMAP host.', $this->domain )
			),
			'imap_port' => array(
				'id' => 'imap_port',
				'name' => __( 'IMAP Port', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'imap',
				'default_value' => '',
				'example' => '993 for SSL',
				'tt_image' => '',
				'tt_description' => __( 'The IMAP port number.', $this->domain )
			),
			'inbox_folder_name' => array(
				'id' => 'inbox_folder_name',
				'name' => __( 'Sent Items Folder', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'imap',
				'default_value' => '',
				'example' => __( 'Sent', $this->domain ),
				'tt_image' => '',
				'tt_description' => __( 'Case sensitive, Gmail only.', $this->domain ) . ' ' . __( 'Use "Sent" in the language you use the mail account with or as it is named in Outlook, Thunderbird etc., respectively).', $this->domain ) . ' ' . __( 'The name of the folder your replies will be stored into on the web server. E.g. Sent, Gesendet, Envoyé, etc.', $this->domain )
			),
			'inbox_folder_path' => array(
				'id' => 'inbox_folder_path',
				'name' => __( 'Sent Items Path', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'imap',
				'default_value' => '',
				'example' => '{imap.gmail.com}[Gmail]/' . __( 'Sent', $this->domain ),
				'tt_image' => '',
				'tt_description' => __('(if Gmail - optional or leave blank)', $this->domain) . ' ' . __( 'Use "Sent" in the language you use the mail account with or as it is named in Outlook, Thunderbird etc., respectively).', $this->domain ) . ' ' . __( 'This is an option to provide an inbox path similar to the one in the example. Use this approach, if the option above should fail (Error BON1704-0001). <br>Otherwise, leave blank.', $this->domain )
			),
			'ssl_certification_validation' => array(
				'id' => 'ssl_certification_validation',
				'name' => __( 'SSL Certification Validation', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'imap',
				'default_value' => 'Yes',
				'example' => __( 'Yes', $this->domain ),
				'values' => array( 'nocert' => __( 'No', $this->domain ), 'cert' => __( 'Yes', $this->domain ) ),
				'tt_image' => '',
				'tt_description' => __( '"No" skips ssl certificate validation. This setting is not secure and you should avoid using it.', $this->domain ) . " " .
				                    __( 'Otherwise, you\'re a potential subject of man in the middle attacks', $this->domain ) . " " .
				                    "(<a href='https://stackoverflow.com/questions/7891729/certificate-error-using-imap-in-php' target='_blank'>" . __( "Read more", $this->domain ) . "</a>)."
			)
		);
	}
	
	/**
	 * Bonaire_Options constructor.
	 *
	 * @param $domain
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain ) {
		
		$this->domain           = $domain;
		$this->default_options  = $this->default_options();
		$stored_options         = $this->stored_options();
		$this->stored_options   = $stored_options;
		$this->account_settings = $this->account_settings( $stored_options );
		$this->options_meta     = $this->options_meta();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
	}
	
	/**
	 * Localizes the javascript file for the admin part of the plugin.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function localize_script() {
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireOptions', $this->get_script_data() );
	}
	
	/**
	 * Assembles the data that needs to be localized to the javascript file again
	 * in order to be up to date after the user saved settings via ajax.
	 *
	 * @return array $data
	 * @since 0.9.6
	 */
	private function get_script_data() {
		
		$Bonaire_Account_Settings_Status = new AdminIncludes\Bonaire_Settings_Status( $this->domain );
		
		$options_meta     = array( 'options_meta' => $this->options_meta() );
		$default_options  = array( 'default_options' => $this->default_options() );
		$has_empty_fields = array( 'has_empty_field' => $this->has_empty_field() );
		$save_reply       = array( 'save_reply' => $this->stored_options->{0}->save_reply );
		$cf7_status       = array( 'cf7_status' => $Bonaire_Account_Settings_Status->get_settings_status( 'cf7', true ) );
		$smtp_status      = array( 'smtp_status' => $Bonaire_Account_Settings_Status->get_settings_status( 'smtp', true ) );
		$imap_status      = array( 'imap_status' => $Bonaire_Account_Settings_Status->get_settings_status( 'imap', true ) );
		$ajaxurl          = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		
		return array_merge( $options_meta, $default_options, $has_empty_fields, $save_reply, $ajaxurl, $cf7_status, $smtp_status, $imap_status );
	}
	
	/**
	 * Updates the data that was previously sent to the javascript file.
	 * This occurs every time the user saves settings on the settings page,
	 * since that process runs with ajax and the page does not reload after saving the options.
	 *
	 * @return bool
	 * @since 0.9.6
	 */
	private function update_localized_data() {
		
		global $wp_scripts;
		
		return $wp_scripts->localize( 'bonaire-admin-js', 'BonaireOptions', $this->get_script_data() );
	}
	
	/**
	 * Checks for empty fields in the stored settings (settings page),
	 * since all of them are necessary to establish a connection via SMTP and / or IMAP.
	 *
	 * @param int $settings_group
	 *
	 * @return bool
	 * @since 0.9.6
	 */
	private function has_empty_field( $settings_group = 0 ) {
		
		if ( 1 === $settings_group ) {
			$keys = $this->smtp_hash_keys;
		} else {
			$keys = $this->imap_hash_keys;
		}
		
		$stored_options   = $this->stored_options();
		$account_settings = $stored_options->{0};
		
		$empty_values = 0;
		foreach ( $keys as $key => $value ) {
			
			if ( ! isset( $account_settings->{$key} ) || '' === $account_settings->{$key} ) {
				
				$empty_values ++;
			}
		}
		
		return 0 !== $empty_values;
	}
	
	/**
	 * Stores the options in the database.
	 *
	 * @param array $input
	 *
	 * @return array|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_save_options( $input ) {
		
		// Copy the currently stored options for later use
		$old_stored_options = get_option( 'bonaire_options' );
		
		// Validate
		$output = $this->validate_options( $input );
		
		// Re-add the stored password if there were no changes and there is one stored already
		if ( ( '*****' === $input['password'] ) ) {
			$output['password'] = $this->account_settings->password;
		} else {
			$output['password'] = sanitize_text_field( $input['password'] );
		}
		
		// Crypt password if needed
		if ( '' !== $input['password'] && '*****' !== $input['password'] ) {
			$output['password'] = $this->crypt( $input['password'], 'e' );
		}
		
		$stored_options     = get_option( 'bonaire_options' );
		$stored_options[0]  = $output;
		
		// Update options
		$result = update_option( 'bonaire_options', $stored_options, true );
		
		if ( false === $result ) {
			
			if ( false === $this->have_settings_changed( $output, $old_stored_options ) ) {
				
				return new WP_Error( - 1, __( 'There\'s nothing to save.', $this->domain ) );
			}
			
			return new WP_Error( - 2, __( 'Can\'t save settings right now.', $this->domain ) . ' ' . __( 'Please try again later.', $this->domain ) . ' (-2)' );
		}
		
		$this->update_localized_data();
		
		return $this->evaluate_account_settings( $output, $old_stored_options );
	}
	
	/**
	 * @param $output
	 * @param $old_stored_options
	 *
	 * @return array|WP_Error
	 * @throws \Exception
	 */
	private function evaluate_account_settings( $output, $old_stored_options ) {
		
		// Test SMTP and / or IMAP settings
		$Bonaire_Account_Evaluator       = new AdminIncludes\Bonaire_Settings_Evaluator( $this->domain, $this );
		$Bonaire_Account_Settings_Status = new AdminIncludes\Bonaire_Settings_Status( $this->domain );
		
		$cf7_result                = null;
		$cf7_status                = $Bonaire_Account_Settings_Status->get_settings_status( 'cf7' );
		$have_cf7_settings_changed = $this->have_settings_changed( $output, $old_stored_options, 'cf7' );
		if ( $have_cf7_settings_changed || false === $have_cf7_settings_changed && 'green' !== $cf7_status ) {
			$cf7_result = $Bonaire_Account_Evaluator->bonaire_test_contact_form();
		}
		
		$imap_result                = null;
		$imap_status                = $Bonaire_Account_Settings_Status->get_settings_status( 'imap' );
		$have_imap_settings_changed = $this->have_settings_changed( $output, $old_stored_options, 'imap' );
		if ( $have_imap_settings_changed || false === $have_imap_settings_changed && 'green' !== $imap_status ) {
			if ( 'no' === $output['save_reply'] ) {
				$status = 'inactive';
				$Bonaire_Account_Settings_Status->set_settings_status( 'imap', $status );
			} else {
				$imap_result = $Bonaire_Account_Evaluator->bonaire_test_imap_settings();
			}
		}
		
		$smtp_result                = null;
		$smtp_status                = $Bonaire_Account_Settings_Status->get_settings_status( 'smtp' );
		$have_smtp_settings_changed = $this->have_settings_changed( $output, $old_stored_options, 'smtp' );
		if ( $have_smtp_settings_changed || false === $have_smtp_settings_changed && 'green' !== $smtp_status ) {
			$smtp_result = $Bonaire_Account_Evaluator->bonaire_test_smtp_settings();
		}
		
		if ( ! is_wp_error( $smtp_result ) && ! is_wp_error( $imap_result ) && ! is_wp_error( $cf7_result ) ) {
			
			return array(
				'success' => true,
				'cf7_status' => $cf7_result['status'],
				'smtp_status' => $smtp_result['status'],
				'imap_status' => $imap_result['status'],
				'message' => false,
				'messages' => false,
				'error_code' => 0
			);
		}
		
		return new WP_Error(
			21,
			__( 'Settings Saved.', $this->domain ),
			array(
				'success' => false,
				'cf7_status' => is_wp_error( $cf7_result ) ? 'orange' : 'green',
				'smtp_status' => is_wp_error( $smtp_result ) ? 'orange' : 'green',
				'imap_status' => is_wp_error( $imap_result ) ? 'orange' : 'green',
				'message' => false,
				'messages' => false,
				'error_code' => 0
			)
		);
	}
	
	/**
	 * Checks for changed email accound settings per protocol.
	 *
	 * @param array $input
	 * @param array $old_stored_options
	 * @param bool $protocol
	 *
	 * @return bool
	 */
	private function have_settings_changed( $input, $old_stored_options, $protocol = false ) {
		
		switch ( $protocol ) {
			
			case "smtp":
				
				return $this->check_for_changed_settings( $this->smtp_hash_keys, $input, $old_stored_options );
			
			case "imap":
				
				return $this->check_for_changed_settings( $this->imap_hash_keys, $input, $old_stored_options );
			
			default:
				
				return $this->check_for_changed_settings( $this->contact_form_hash_keys, $input, $old_stored_options );
		}
	}
	
	/**
	 * Checks for changes in the email account settings.
	 *
	 * @param $keys
	 * @param $input
	 * @param $old_stored_options
	 *
	 * @return bool
	 */
	private function check_for_changed_settings( $keys, $input, $old_stored_options ) {
		
		// Extract the relevant values for comparison
		$input_array_to_check          = array();
		$stored_options_array_to_check = array();
		foreach ( $keys as $key => $value ) {
			$input_array_to_check[ $key ]          = $input[ $key ];
			$stored_options_array_to_check[ $key ] = $old_stored_options[0][ $key ];
		}
		
		// Decrypt the password for the check, if it got changed
		if ( '*****' !== $input['password'] && $input['password'] !== $old_stored_options[0]['password'] ) {
			$stored_options_array_to_check['password'] = $this->crypt( $old_stored_options[0]['password'], $action = 'd' );
		}
		
		$result = array_diff_assoc( $input_array_to_check, $stored_options_array_to_check );
		unset( $old_stored_options );
		unset( $input_array_to_check );
		unset( $stored_options_array_to_check );
		
		return ! empty( $result );
	}
	
	/**
	 * Resets the options in the database to the default ones.
	 * Error codes:
	 * -1 This error indicates saving options while there were no changes made
	 * -2 This error indicates a general problem while saving options
	 * -3 This error indicates a general problem while resetting options
	 *
	 * @return array|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function reset_options() {
		
		$Bonaire_Account_Settings_Status = new AdminIncludes\Bonaire_Settings_Status( $this->domain );
		
		delete_option( 'bonaire_options' );
		$default_settings = $this->default_options();
		$settings[0]      = (array) $default_settings->{0};
		$settings[1]      = (array) $default_settings->{1};
		$result           = update_option( 'bonaire_options', $settings, true );
		
		if ( false !== $result ) {
			
			$Bonaire_Account_Settings_Status->set_settings_status( $protocol = 'smtp', 'orange' );
			$Bonaire_Account_Settings_Status->set_settings_status( $protocol = 'imap', 'orange' );
			
			$this->update_localized_data();
			
			return array(
				'success' => true,
				'smtp_status' => 'orange',
				'imap_status' => 'orange',
				'message' => false,
				'messages' => false,
				'error_code' => 0
			);
		}
		
		return new WP_Error(
			21,
			__( 'Failed to reset settings. Please refresh the page and try again.', $this->domain ),
			array(
				'success' => false,
				'smtp_status' => 'orange',
				'imap_status' => 'orange',
				'message' => false,
				'messages' => false,
				'error_code' => 0
			)
		);
	}
	
	/**
	 * Sanitizes and validates the user inputs.
	 *
	 * @param array $input
	 *
	 * @return array $output
	 * @since 0.9.6
	 */
	public function validate_options( $input ) {
		
		$output = null;
		foreach ( $input as $key => $value ) {
			
			// Sanitize value
			$value = strip_tags( stripslashes( $value ) );
			
			if ( 'form_id' === $key || 'number_posts' === $key ) {
				$output[ $key ] = is_int( (int) $value ) ? (string) $value : '';
			} elseif ( 'smtp_host' === $key || 'imap_host' === $key ) {
				$result         = preg_match( '/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i', $value );
				$output[ $key ] = 1 === $result ? $value : '';
			} elseif ( 'smtpauth' === $key ) {
				$output[ $key ] = true;
			} elseif ( 'smtp_port' === $key || 'imap_port' === $key ) {
				$result         = filter_var( $value, FILTER_VALIDATE_INT );
				$output[ $key ] = is_int( $result ) && 0 !== $result && 1 !== $result ? (int) $value : '';
			} elseif ( 'username' === $key ) {
				if ( strpos( $value, '@' ) !== false && strpos( $value, '.' ) !== false ) {
					$result         = filter_var( $value, FILTER_VALIDATE_EMAIL );
					$output[ $key ] = false !== $result ? $value : '';
				} else {
					$result         = preg_match( '/^[A-Za-z0-9 _.-]+$/', $value );
					$output[ $key ] = 1 === $result ? $value : '';
				}
			} elseif ( 'password' === $key ) {
				$output[ $key ] = $value;
			} elseif ( 'channel' === $key ) {
				$output[ $key ] = (string) $value;
			} elseif ( 'smtpsecure' === $key || 'imapsecure' === $key ) {
				if ( 'ssl' !== $value && 'tls' !== $value ) {
					$output[ $key ] = 'ssl';
				} else {
					$output[ $key ] = $value;
				}
			} elseif ( 'save_reply' === $key ) {
				if ( 'no' !== $value ) {
					$output[ $key ] = 'yes';
				} else {
					$output[ $key ] = 'no';
				}
			} elseif ( 'inbox_folder_name' === $key || 'inbox_folder_path' === $key ) {
				$output[ $key ] = $value;
			} elseif ( 'ssl_certification_validation' === $key ) {
				if ( 'nocert' !== $value ) {
					$output[ $key ] = 'cert';
				} else {
					$output[ $key ] = 'nocert';
				}
			} elseif ( 'from' === $key ) {
				$result         = filter_var( $value, FILTER_VALIDATE_EMAIL );
				$output[ $key ] = false !== $result ? $value : '';
			} elseif ( 'fromname' === $key ) {
				$result         = preg_match( '/^[A-Za-z0-9 _.-]+$/', $value );
				$output[ $key ] = 1 === $result ? $value : '';
			} elseif ( 'your_name' === $key || 'your_email' === $key || 'your_subject' === $key || 'your_message' === $key ) {
				$result         = preg_match( '/^[A-Za-z0-9_-]+$/', $value );
				$output[ $key ] = 1 === $result ? $value : '';
			}
		}
		
		return apply_filters( array( $this, 'validate_options' ), $output, $input );
	}
	
	/**
	 * Encrypts and decrypts the password for the email account stored for replies.
	 *
	 * @param string $string
	 * @param string $action
	 *
	 * @return string $output|bool
	 * @since 0.9.6
	 * @see   \Bonaire\Admin\Includes\Bonaire_Mail decrypt()
	 */
	private function crypt( $string, $action = 'e' ) {
		
		$secret_key = defined(AUTH_KEY) ? AUTH_KEY : 'r4RWH*ynn!AS.|A-j<qph!#))@!Gde5i,0&Z[R=i.]78f[Ine)aChIMwRpqZN$6~';
		$secret_iv  = defined(AUTH_SALT) ? AUTH_SALT : '=;.6h~xr5v/BZuKP-|GR B*Kb`K-Q@PH6r>My6=-gz$qTt+X!0Rc_6>N:&g5&1>R';
		
		if ( '' === $secret_key || '' === $secret_iv ) {
			return $string;
		}
		
		$output         = false;
		$encrypt_method = 'AES-256-CBC';
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		
		if ( $action === 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		} elseif ( $action === 'd' ) {
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
		
		return $output;
	}
	
	/**
	 * Returns the stored options per default or
	 * internal data such as the settings hashes.
	 *
	 * @param int $settings_group
	 *
	 * @return object
	 * @since 0.9.6
	 */
	public function get_stored_options( $settings_group = 0 ) {
		
		$stored_options = get_option( 'bonaire_options' );
		
		if ( false === $stored_options && $settings_group === 0 ) {
			return $this->default_options()->{0};
		} elseif ( false === $stored_options && $settings_group === 1 ) {
			return $this->default_options()->{1};
		}
		
		if ( 1 === $settings_group ) {
			
			return (object) $stored_options[1];
		}
		
		return (object) $stored_options[0];
	}
	
	/**
	 * Returns either the options meta data or
	 * a single options attribute value.
	 *
	 * @param null $attrbute_name
	 * @param null $attribute
	 *
	 * @return object|string
	 * @since 0.9.6
	 */
	public function get_options_meta( $attrbute_name = null, $attribute = null ) {
		
		if ( 'option_keys' === $attrbute_name ) {
			
			$list = array();
			foreach ( (array) $this->options_meta as $key => $value ) {
				$list[ $key ] = '';
			}
			
			return (object) $list;
		}
		
		if ( null !== $attrbute_name && null === $attribute ) {
			
			return $this->options_meta->{$attrbute_name};
		}
		
		if ( null !== $attrbute_name && null !== $attribute ) {
			
			return $this->options_meta->{$attrbute_name}[ $attribute ];
		}
		
		return $this->options_meta;
	}
	
}
