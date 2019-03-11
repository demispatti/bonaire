<?php

namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for handling the user options.
 *
 * @since             0.9.0
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Options {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.0
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the default options.
	 *
	 * @var      object $default_options
	 * @since    0.9.0
	 * @access   public
	 */
	public $default_options;
	
	/**
	 * Holds the stored options.
	 *
	 * @var      object $stored_options
	 * @since    0.9.0
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Holds the account settings part of the stored options.
	 *
	 * @var      object $account_settings
	 * @since    0.9.0
	 * @access   private
	 */
	private $account_settings;
	
	/**
	 * Holds the options meta data.
	 *
	 * @var      object $options_meta
	 * @since    0.9.0
	 * @access   private
	 */
	private $options_meta;
	
	/**
	 * The the option keys that are used to create
	 * the SMTP hash key.
	 *
	 * @var      array $smtp_hash_keys
	 * @since    0.9.0
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
	 * The the option keys that are used to create
	 * the IMAP hash key.
	 *
	 * @var      array $imap_hash_keys
	 * @since    0.9.0
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
		'use_ssl_certification_validation' => '',
	);
	
	/**
	 * Returns the default options.
	 *
	 * @since 0.9.0
	 * @return object $options
	 */
	private function default_options() {
		
		$options = (object) array();
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
			'use_ssl_certification_validation' => 'cert',
		);
		$options->{1} = (object) array(
			'smtp_hash' => md5( serialize( array(
				'channel' => '',
				'username' => '',
				'password' => '',
				'smtp_host' => '',
				'smtp_port' => 465,
				'smtpsecure' => 'ssl',
				'fromname' => '',
				'from' => ''
			) ) ),
			'imap_hash' => md5( serialize( array(
				'channel' => '',
				'username' => '',
				'password' => '',
				'smtp_host' => '',
				'smtp_port' => 465,
				'smtpsecure' => 'ssl',
				'fromname' => '',
				'from' => '',
				'imapsecure' => 'ssl',
				'imap_host' => '',
				'imap_port' => 993,
				'use_ssl_certification_validation' => 'cert',
			) ) ),
			'smtp_settings_state' => 'red',
			'imap_settings_state' => 'red'
		);
		
		return $options;
	}
	
	/**
	 * Returns the stored options or the default options as a fallback.
	 *
	 * @since 0.9.0
	 * @return object $options
	 */
	private function stored_options() {
		
		$stored_options = get_option( 'bonaire_options' );
		
		if ( false === $stored_options ) {
			
			return $this->default_options;
		}
		
		$stored_options[0]['password'] = $this->bonaire_crypt( $stored_options[0]['password'], 'd' );
		
		$options = (object) array();
		$options->{0} = (object) $stored_options[0];
		$options->{1} = (object) $stored_options[1];
		
		return $options;
	}
	
	/**
	 * Returns the email account settings or the default settings if none were saved yet.
	 *
	 * @param object $stored_options
	 *
	 * @since 0.9.0
	 * @return object
	 */
	private function account_settings( $stored_options ) {
		
		$account_settings = isset( $stored_options->{0} ) ? $stored_options->{0} : new \stdClass();
		
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
	 * @since 0.9.0
	 * @return object $options_meta
	 */
	private function options_meta() {
		
		$options_meta = (object) array(
			'channel' => array(
				'id' => 'channel',
				'name' => __( 'Contact Form Title', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => __( 'none', $this->domain ),
				'example' => __( 'Contact form 1', $this->domain ),
				'values' => array( 'none' => __( 'none', $this->domain ) ),
				'tt_image' => BONAIRE_ROOT_URL . 'admin/images/tooltips/tt-channel.jpg',
				'tt_description' => __( 'The Title of the contactform you want to use this plugin with. Usually it is the form that\'s displayed on the contact page of your website.', $this->domain )
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
			'use_ssl_certification_validation' => array(
				'id' => 'use_ssl_certification_validation',
				'name' => __( 'Use SSL Certification Validation', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'imap',
				'default_value' => 'cert',
				'example' => __( 'cert', $this->domain ),
				'values' => array( 'nocert' => 'nocert', 'cert' => 'cert' ),
				'tt_image' => '',
				'tt_description' => __( '"nocert" Skips the ssl certificate validation. This setting is not secure and you should avoid using it.', $this->domain )
			),
		);
		
		return $options_meta;
	}
	
	/**
	 * Bonaire_Options constructor.
	 *
	 * @param $domain
	 *
	 * @since 0.9.0
	 * @return void
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
		$this->default_options = $this->default_options();
		$stored_options = $this->stored_options();
		$this->stored_options = $stored_options;
		$this->account_settings = $this->account_settings( $stored_options );
		$this->options_meta = $this->options_meta();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @since 0.9.0
	 * @return void
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
	}
	
	/**
	 * Localizes the javascript file for the admin part of the plugin.
	 *
	 * @since 0.9.0
	 * @return void
	 */
	public function localize_script() {
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireOptions', $this->get_script_data() );
	}
	
	/**
	 * Assembles the data that needs to be localized to the javascript file again
	 * in order to be up to date after the user saved settings via ajax.
	 *
	 * @since 0.9.0
	 * @return array $data
	 */
	private function get_script_data() {
		
		$options_meta = array( 'options_meta' => $this->options_meta() );
		$default_options = array( 'default_options' => $this->default_options() );
		$has_empty_fields = array( 'has_empty_field' => $this->has_empty_field() );
		$save_reply = array( 'save_reply' => $this->stored_options->{0}->save_reply );
		$ajaxurl = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		
		$data = array_merge( $options_meta, $default_options, $has_empty_fields, $save_reply, $ajaxurl );
		
		return $data;
	}
	
	/**
	 * Updates the data that was previously sent to the javascript file.
	 * This occurs every time the user saves settings on the settings page,
	 * since that process runs with ajax and the page does not reload after saving the options.
	 *
	 * @since 0.9.0
	 * @return bool
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
	 * @since 0.9.0
	 * @return bool
	 */
	private function has_empty_field( $settings_group = 0 ) {
		
		if ( 1 === $settings_group ) {
			$keys = $this->smtp_hash_keys;
		} else {
			$keys = $this->imap_hash_keys;
		}
		
		$stored_options = $this->stored_options();
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
	 * @since 0.9.0
	 * @return array|\WP_Error
	 */
	public function bonaire_save_options( $input ) {
		
		// Re-add stored password if there was one and there are no changes
		if ( '' !== $this->account_settings->password && ( '*****' === $input['password'] || '' === $input['password'] ) ) {
			$input['password'] = $this->account_settings->password;
		}
		
		// Validate
		$output = $this->validate_options( $input );
		if ( '' !== $output['password'] && '*****' !== $output['password'] ) {
			$output['password'] = $this->bonaire_crypt( $output['password'], 'e' );
		}
		
		$stored_options = get_option( 'bonaire_options' );
		$stored_options[0] = $output;
		
		// Update options
		$result = update_option( 'bonaire_options', $stored_options, true );
		
		if ( false === $result ) {
			
			$diff = array_diff( $input, (array) $this->account_settings );
			
			if ( empty( $diff ) ) {
				
				return new \WP_Error( - 1, __( 'There\'s nothing new to save.', $this->domain ) );
			}
			
			return new \WP_Error( - 2, __( 'Failed to save settings.', $this->domain ) );
		}
		
		$this->update_localized_data();
		
		return $this->set_settings_states();
	}
	
	/**
	 * Resets the options in the database to the default ones.
	 * Error codes:
	 * -1 This error indicates saving options while there were no changes made
	 * -2 This error indicates a general problem while saving options
	 * -3 This error indicates a general problem while resetting options
	 *
	 * @since 0.9.0
	 * @return array|\WP_Error
	 */
	public function reset_options() {
		
		delete_option( 'bonaire_options' );
		
		$default_settings = $this->default_options();
		$result = update_option( 'bonaire_options', $default_settings, true );
		
		if ( false !== $result ) {
			
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'red' );
			$this->bonaire_set_evaluation_state( $protocol = 'imap', 'red' );
			
			$this->update_localized_data();
			
			return $this->set_settings_states();
		}
		
		return new \WP_Error( - 3, __( 'Failed to reset settings. Please refresh the page and try again.', $this->domain ) );
	}
	
	/**
	 * Sanitizes and validates the user inputs.
	 *
	 * @param array $input
	 *
	 * @since 0.9.0
	 * @return array $output
	 */
	public function validate_options( $input ) {
		
		$output = null;
		foreach ( $input as $key => $value ) {
			
			// Sanitize value
			$value = strip_tags( stripslashes( $value ) );
			
			if ( 'smtp_host' === $key || 'imap_host' === $key ) {
				$result = preg_match( '/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i', $value );
				$output[ $key ] = 1 === $result ? $value : '';
			}
			if ( 'smtpauth' === $key ) {
				$output[ $key ] = true;
			}
			if ( 'smtp_port' === $key || 'imap_port' === $key ) {
				$result = filter_var( $value, FILTER_VALIDATE_INT );
				$output[ $key ] = is_int( $result ) && 0 !== $result && 1 !== $result ? (int) $value : '';
			}
			if ( 'username' === $key ) {
				if ( strpos( $value, '@' ) !== false && strpos( $value, '.' ) !== false ) {
					$result = filter_var( $value, FILTER_VALIDATE_EMAIL );
					$output[ $key ] = false !== $result ? $value : '';
				} else {
					$result = preg_match( '/^[A-Za-z0-9 _.-]+$/', $value );
					$output[ $key ] = 1 === $result ? $value : '';
				}
			}
			if ( 'password' === $key ) {
				$output[ $key ] = $value;
			}
			if ( 'channel' === $key ) {
				$output[ $key ] = (string) $value;
			}
			if ( 'smtpsecure' === $key || 'imapsecure' === $key ) {
				if ( 'ssl' !== $value && 'tls' !== $value ) {
					$output[ $key ] = 'ssl';
				} else {
					$output[ $key ] = $value;
				}
			}
			if ( 'save_reply' === $key ) {
				if ( 'no' !== $value ) {
					$output[ $key ] = 'yes';
				} else {
					$output[ $key ] = 'no';
				}
			}
			if ( 'use_ssl_certification_validation' === $key ) {
				if ( 'nocert' !== $value ) {
					$output[ $key ] = 'cert';
				} else {
					$output[ $key ] = 'nocert';
				}
			}
			
			if ( 'from' === $key ) {
				$result = filter_var( $value, FILTER_VALIDATE_EMAIL );
				$output[ $key ] = false !== $result ? $value : '';
			}
			if ( 'fromname' === $key ) {
				$result = preg_match( '/^[A-Za-z0-9 _.-]+$/', $value );
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
	 * @since 0.9.0
	 * @return string $output|bool
	 */
	private function bonaire_crypt( $string, $action = 'e' ) {
		
		$secret_key = AUTH_KEY;
		$secret_iv = AUTH_SALT;
		
		if ( '' === $secret_key || '' === $secret_iv ) {
			return $string;
		}
		
		$output = false;
		$encrypt_method = 'AES-256-CBC';
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		
		if ( $action === 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		} elseif ( $action === 'd' ) {
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
		
		return $output;
	}
	
	/**
	 * Stores the result of the settings evaluation for either SMTP or IMAP to the database.
	 *
	 * @param string $protocol
	 * @param string $state
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 */
	public function bonaire_set_evaluation_state( $protocol, $state ) {
		
		$stored_options = get_option( 'bonaire_options' );
		$stored_options[1][ $protocol . '_settings_state' ] = $state;
		
		try {
			update_option( 'bonaire_options', $stored_options, true );
			
			switch ( $state ) {
				case 'green':
					
					return $this->store_hash( $protocol );
					break;
				case 'orange':
				
				case 'red':
					
					break;
			}
		} catch( Exception $e ) {
			
			return new \WP_Error( 1, __( 'Internal Error: Unable to set evaluation state.', $this->domain ) );
		}
		
		return true;
	}
	
	/**
	 * Returns true if positively evaluated email account settings were found,
	 * otherwise it returns false.
	 *
	 * @param string $protocol
	 *
	 * @since 0.9.0
	 * @return bool
	 */
	public function get_settings_state( $protocol = 'smtp' ) {
		
		return 'green' === $this->get_stored_options( 1 )->{$protocol . '_settings_state'};
	}
	
	/**
	 * Sets the state of the evaluated email account settings.
	 * The settings are:
	 * - green, the settings were successfully evaluated. Replies to messages can be sent / stored in 'INBOX.Sent' (if IMAP is configuerd)
	 * - orange, the email account settings are complete but evaluation failed
	 * - red, the email account settings are incomplete (empty input fields left on the settings page)
	 *
	 * @since 0.9.0
	 * @return array $states
	 */
	private function set_settings_states() {
		
		$states = array();
		
		$Bonaire_Mail = new AdminIncludes\Bonaire_Mail( $this->domain, $this->get_stored_options( 0 ) );
		/**
		 * @var Bonaire_Settings_Evaluator $Bonaire_Settings_Evaluator
		 */
		$Bonaire_Settings_Evaluator = new AdminIncludes\Bonaire_Settings_Evaluator( $this->domain, $this, $Bonaire_Mail );
		
		if ( true === $Bonaire_Settings_Evaluator->get_settings_md5_match( 'smtp' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'green' );
		}
		if ( true === $Bonaire_Settings_Evaluator->get_settings_md5_match( 'imap' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'imap', 'green' );
		}
		
		if ( false === $Bonaire_Settings_Evaluator->get_settings_md5_match( 'smtp' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'orange' );
			$states['smtp_state'] = 'orange';
		}
		if ( false === $Bonaire_Settings_Evaluator->get_settings_md5_match( 'imap' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'imap', 'orange' );
			$states['imap_state'] = 'orange';
		}
		if ( true === $this->has_empty_field( 'smtp' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'red' );
			$states['smtp_state'] = 'red';
		}
		if ( true === $this->has_empty_field( 'imap' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'imap', 'red' );
			$states['imap_state'] = 'red';
		}
		
		return $states;
	}
	
	/**
	 * Stores the created hash value.
	 *
	 * @param string $protocol
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 */
	private function store_hash( $protocol = 'smtp' ) {
		
		$current_settings_hash = $this->create_settings_hash( $protocol );
		$stored_settings_hash = $this->stored_options->{1}->{$protocol . '_hash'};
		if ( $stored_settings_hash === $current_settings_hash ) {
			
			return true;
		}
		
		try {
			$stored_options = get_option( 'bonaire_options' );
			$stored_options[1][ $protocol . '_hash' ] = $current_settings_hash;
			
			return update_option( 'bonaire_options', $stored_options, true );
		} catch( Exception $e ) {
			
			return new \WP_Error( 1, __( 'Internal Error: Unable to store plugin data.', $this->domain ) );
		}
	}
	
	/**
	 * Creates a hash value based on either the SMTP settings for SMTP
	 * or the IMAP AND SMTP settings for IMAP.
	 *
	 * @param string $protocol
	 *
	 * @since 0.9.0
	 * @return string
	 */
	private function create_settings_hash( $protocol ) {
		
		$array = array();
		$option_keys = $this->{$protocol . '_hash_keys'};
		foreach ( $option_keys as $key => $value ) {
			$array[ $key ] = $this->account_settings->{$key};
		}
		
		return md5( serialize( $array ) );
	}
	
	/**
	 * Returns the stored options per default or
	 * internal data such as the settings hashes.
	 *
	 * @param int $settings_group
	 *
	 * @since 0.9.0
	 * @return object
	 */
	public function get_stored_options( $settings_group = 0 ) {
		
		if ( 1 === $settings_group ) {
			
			return (object) $this->stored_options->{1};
		}
		
		return (object) $this->stored_options->{0};
	}
	
	/**
	 * Returns either the options meta data or
	 * a single options attribute value.
	 *
	 * @param null $attrbute_name
	 * @param null $attribute
	 *
	 * @since 0.9.0
	 * @return object|string
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
	
	/**
	 * Returns a bool indicating wether the account settings are complete or not.
	 *
	 * @param int $settings_group
	 *
	 * @since 0.9.0
	 * @return bool
	 */
	public function get_has_empty_field( $settings_group ) {
		
		return $this->has_empty_field( $settings_group );
	}
	
}
