<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bonaire_Options {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	/**
	 * Holds the stored options.
	 *
	 * @var stdClass $default_options
	 */
	public $default_options;
	
	/**
	 * Holds the stored options.
	 *
	 * @var stdClass $stored_options
	 */
	private $stored_options;
	
	/**
	 * Holds the stored options.
	 *
	 * @var stdClass $account_settings
	 */
	private $account_settings;
	
	/**
	 * Holds the options meta data,
	 * like name, placeholder, description
	 *
	 * @var stdClass $options_meta
	 */
	private $options_meta;
	
	/**
	 * @var array $smtp_hash_keys
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
	 * @var array $imap_hash_keys
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
	);
	
	private function default_options() {
		
		$options = array(
			0 => array(
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
			),
			1 => array(
				'smtp_hash' => md5( serialize( array(
					'username' => '',
					'password' => '',
					'smtp_host' => '',
					'smtp_port' => 465,
					'smtpsecure' => 'ssl',
					'fromname' => '',
					'from' => ''
				) ) ),
				'imap_hash' => md5( serialize( array(
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
				) ) ),
				'smtp_settings_state' => 'red',
				'imap_settings_state' => 'red'
			)
		);
		
		return $options;
	}
	
	/**
	 * Returns the stored options or the default options as a fallback.
	 *
	 * @return \stdClass
	 */
	private function stored_options() {
		
		$stored_options = get_option( 'bonaire_options' );
		
		if ( false !== $stored_options ) {
			$stored_options[0]['password'] = $this->bonaire_crypt( $stored_options[0]['password'], 'd' );
		}
		if ( false === $stored_options ) {
			$stored_options = $this->default_options;
		}
		
		$options = new stdClass();
		$options->{0} = (object) $stored_options[0];
		$options->{1} = (object) $stored_options[1];
		
		return $options;
	}
	
	private function account_settings( $stored_options ) {
		
		$account_settings = isset( $stored_options->{0} ) ? $stored_options->{0} : false;
		
		if ( false !== $account_settings ) {
			
			return $account_settings;
		}
		
		return $this->default_options->{0};
	}
	
	/**
	 * Holds the potions meta data.
	 *
	 * @return mixed object | string
	 */
	private function options_meta() {
		
		return (object) array(
			'username' => array(
				'id' => 'username',
				'name' => __( 'Username', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The username for the email account.', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => 'e.g. your name or email',
				'error_message' => __( 'Please enter your username.', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'password' => array(
				'id' => 'password',
				'name' => __( 'Password', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The password for the email account.', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => '',
				'error_message' => __( 'Please enter your password.', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'smtp_host' => array(
				'id' => 'smtp_host',
				'name' => __( 'SMTP Host', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The smtp address for your email account. ', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => 'e.g. smtp.gmail.com',
				'error_message' => __( 'Enter a url in a valid format, e.g.: smtp.gmail.com', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'smtp_port' => array(
				'id' => 'smtp_port',
				'name' => __( 'SMTP Port', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The SMTP port.', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => '',
				'example' => 'e.g. 465',
				'error_message' => __( 'Enter integers only.', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'smtpauth' => array(
				'id' => 'smtpauth',
				'name' => __( 'SMTPAuth', $this->domain ),
				'placeholder' => '',
				'description' => __( 'User name and passwort must be provided in order to send messages (default setting).', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'general',
				'default_value' => 'true',
				'example' => 'e.g. true',
				'error_message' => '',
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'smtpsecure' => array(
				'id' => 'smtpsecure',
				'name' => __( 'SMTPSecure', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The encryption method.', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'smtp',
				'default_value' => 'SSL',
				'values' => array( 'tls' => 'TLS', 'ssl' => 'SSL' ),
				'example' => 'e.g. SSL or TLS',
				'error_message' => __( 'Please enter either SSL or TLS', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'fromname' => array(
				'id' => 'fromname',
				'name' => __( 'From Name', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The senders\'s name, in most cases this will be your name.', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'account',
				'default_value' => '',
				'example' => 'e.g. John Doe',
				'error_message' => __( 'Please enter alphanumeric values and spaces only.', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'from' => array(
				'id' => 'from',
				'name' => __( 'From', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The email account\'s email address.', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'account',
				'default_value' => '',
				'example' => 'e.g. yourname@domain.com',
				'error_message' => __( 'Enter email address.', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'save_reply' => array(
				'id' => 'save_reply',
				'name' => __( 'Save Reply', $this->domain ),
				'placeholder' => '',
				'description' => __( 'Enable / disable option to save replies in the "sent items" folder of your email account.', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'none',
				'default_value' => 'No',
				'values' => array( 'no' => __( 'No', $this->domain ), 'yes' => __( 'Yes', $this->domain ) ),
				'example' => 'e.g. Yes',
				'error_message' => '',
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'imapsecure' => array(
				'id' => 'imapsecure',
				'name' => __( 'IMAPSecure', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The encryption method.', $this->domain ),
				'type' => 'dropdown',
				'setting' => true,
				'group' => 'imap',
				'default_value' => 'SSL',
				'values' => array( 'tls' => 'TLS', 'ssl' => 'SSL' ),
				'example' => 'e.g. SSL or TLS',
				'error_message' => __( 'Please enter either SSL or TLS', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'imap_host' => array(
				'id' => 'imap_host',
				'name' => __( 'IMAP Host', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The imap address for your email account. ', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'imap',
				'default_value' => '',
				'example' => 'e.g. imap.gmail.com',
				'error_message' => __( 'Enter a url in a valid format, e.g.: imap.gmail.com', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			),
			'imap_port' => array(
				'id' => 'imap_port',
				'name' => __( 'IMAP Port', $this->domain ),
				'placeholder' => '',
				'description' => __( 'The IMAP port.', $this->domain ),
				'type' => 'text',
				'setting' => true,
				'group' => 'imap',
				'default_value' => '',
				'example' => 'e.g. 993',
				'error_message' => __( 'Enter integers only.', $this->domain ),
				'tt_image' => '',
				'tt_blockquote' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_description' => __( 'general name for the post type, usually plural. The same and overridden by "$post_type_object->label".<br>Default is Posts/Pages', $this->domain ),
				'tt_link_to_codex' => 'https://codex.wordpress.org/Function_Reference/register_post_type#labels',
				'tt_link_to_source' => ''
			)
		);
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
		$this->default_options = $this->default_options();
		$stored_options = $this->stored_options();
		$this->stored_options = $stored_options;
		$this->account_settings = $this->account_settings( $stored_options );
		$this->options_meta = $this->options_meta();
	}
	
	/**
	 * Adds all necessary hooks for this class to work.
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
	}
	
	/**
	 * Localizes the script
	 */
	public function localize_script() {
		
		$options_meta = array( 'options_meta' => $this->options_meta() );
		$default_options = array( 'default_options' => (array) $this->default_options() );
		$has_empty_fields = array( 'has_empty_field' => $this->has_empty_field() );
		$data = array_merge( $options_meta, $default_options, $has_empty_fields );
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireOptions', $data );
	}
	
	/**
	 * CHecks for empty fields in the stored settings,
	 * since all of them are necessary to establish a connection via smtp.
	 *
	 * @return string
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
			
			if ( '' === $account_settings->{$key} ) {
				
				$empty_values ++;
			}
		}
		if ( 0 === $empty_values ) {
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Stores the options in the database.
	 *
	 * @param $input
	 *
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
				
				return new WP_Error( - 1, __( 'There\'s nothing to save.', $this->domain ) );
			}
			
			return new WP_Error( - 2, __( 'Failed to save settings.', $this->domain ) );
		}
		
		return $this->set_settings_states();
	}
	
	/**
	 * Resets the options in the database to the default ones.
	 * Error codes:
	 * -1 This error indicates saving options while there were no changes made
	 * -2 This error indicates a general problem while saving options
	 * -3 This error indicates a general problem while resetting options
	 *
	 * @return array|\WP_Error
	 */
	public function reset_options() {
		
		delete_option( 'bonaire_options' );
		
		$default_settings = $this->default_options();
		$result = update_option( 'bonaire_options', $default_settings );
		
		if ( false !== $result ) {
			
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'red' );
			$this->bonaire_set_evaluation_state( $protocol = 'imap', 'red' );
			
			return $this->set_settings_states();/*array('smtp_state' => 'red', 'imap_state' => 'red');*/
		}
		
		return new WP_Error( - 3, __( 'Failed to reset settings. Please refresh the page and try again.', $this->domain ) );
	}
	
	/**
	 * Sanitizes and validates the user inputs.
	 *
	 * @param $input
	 *
	 * @return array
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
				if ( 'true' !== $value ) {
					$output[ $key ] = true;
				} else {
					$output[ $key ] = true;
				}
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
			if ( 'smtpsecure' === $key || 'imapsecure' === $key ) {
				if ( 'ssl' !== $value && 'tls' !== $value ) {
					$output[ $key ] = 'ssl';
				} else {
					$output[ $key ] = $value;
				}
			}
			if ( 'save_reply' === $key ) {
				if ( 'no' !== $value && 'yes' !== $value ) {
					$output[ $key ] = 'no';
				} else {
					$output[ $key ] = $value;
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
	 * @param        $string
	 * @param string $action
	 *
	 * @return bool|string
	 */
	private function bonaire_crypt( $string, $action = 'e' ) {
		
		$secret_key = AUTH_KEY;
		$secret_iv = AUTH_SALT;
		
		if ( '' === $secret_key || '' === $secret_iv ) {
			return $string;
		}
		
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		
		if ( $action === 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		} elseif ( $action === 'd' ) {
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
		
		return $output;
	}
	
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
			
			return new WP_Error( 1, __( 'Internal Error: Unable to set evaluation state.', $this->domain ) );
		}
		
		return true;
	}
	
	private function set_settings_states() {
		
		$states = array();
		
		$Bonaire_Mail = new Bonaire_Mail( $this->domain, $this->get_stored_options( 0 ) );
		/**
		 * @var Bonaire_Settings_Evaluator $Bonaire_Settings_Evaluator
		 */
		$Bonaire_Settings_Evaluator = new Bonaire_Settings_Evaluator( $this->domain, $this, $Bonaire_Mail );
		
		if ( true === $Bonaire_Settings_Evaluator->get_is_valid_account_settings( 'smtp' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'green' );
		}
		if ( true === $Bonaire_Settings_Evaluator->get_is_valid_account_settings( 'imap' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'imap', 'green' );
		}
		
		if ( false === $Bonaire_Settings_Evaluator->get_is_valid_account_settings( 'smtp' ) ) {
			$this->bonaire_set_evaluation_state( $protocol = 'smtp', 'orange' );
			$states['smtp_state'] = 'orange';
		}
		if ( false === $Bonaire_Settings_Evaluator->get_is_valid_account_settings( 'imap' ) ) {
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
	
	private function store_hash( $protocol = 'smtp' ) {
		
		$stored_settings_hash = $this->create_settings_hash( $protocol );
		$current_settings_hash = $this->stored_options->{1}->{$protocol . '_hash'};
		if ( $current_settings_hash === $stored_settings_hash . 'abc' ) {
			
			return true;
		}
		
		try {
			$stored_options = get_option( 'bonaire_options' );
			$stored_options[1][ $protocol . '_hash' ] = $current_settings_hash;
			
			return update_option( 'bonaire_options', $stored_options, true );
		} catch( Exception $e ) {
			
			return new WP_Error( 1, __( 'Internal Error: Unable to store plugin data. (fon update options)', $this->domain ) );
		}
	}
	
	private function create_settings_hash( $protocol ) {
		
		$array = array();
		$option_keys = $this->{$protocol . '_hash_keys'};
		foreach ( $option_keys as $key => $value ) {
			$array[ $key ] = $this->account_settings->{$key};
		}
		
		return md5( serialize( $array ) );
	}
	
	/**
	 * Returns all stored options or the value for one attribute only.
	 *
	 * @param null $key
	 *
	 * @return bool|object|\stdClass|int|string
	 */
	public function get_stored_options( $settings_group = 0 ) {
		
		if ( 1 === $settings_group ) {
			
			return (object) $this->stored_options->{1};
		}
		
		return (object) $this->stored_options->{0};
	}
	
	/**
	 * @param null $attrbute_name
	 * @param null $attribute
	 *
	 * @return mixed|object
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
	
	public function get_has_empty_field( $settings_group ) {
		
		return $this->has_empty_field( $settings_group );
	}
	
}
