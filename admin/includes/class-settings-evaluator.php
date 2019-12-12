<?php

namespace Bonaire\Admin\Includes;

use Exception;
use PHPMailer;
use Bonaire\Admin\Includes as AdminIncludes;
use WP_Error;
use WPCF7_ContactForm;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'PHPMailer' ) ) {
	include ABSPATH . 'wp-includes/class-phpmailer.php';
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WPCF7_ContactForm' ) && file_exists( BONAIRE_PLUGINS_ROOT_DIR . 'contact-form-7/includes/contact-form.php' ) ) {
	include BONAIRE_PLUGINS_ROOT_DIR . 'contact-form-7/includes/contact-form.php';
}

/**
 * The class responsible for email functionality.
 *
 * @since             1.0.0
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
final class Bonaire_Settings_Evaluator extends PHPMailer {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   protected
	 */
	protected $domain;
	
	/**
	 * Holds the instance of the class responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * The class responsible for setting the account settings status.
	 *
	 * @var AdminIncludes\Bonaire_Settings_Status $Bonaire_Account_Settings_Status
	 * @since    1.0.0
	 * @access   private
	 */
	private $Bonaire_Account_Settings_Status;
	
	/**
	 * Holds the stored options.
	 *
	 * @var object $stored_options
	 * @since    0.9.6
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Holds the options meta data.
	 *
	 * @var object $options_meta
	 * @since    0.9.6
	 * @access   private
	 */
	private $options_meta;
	
	/**
	 * Sets up an instance of the mailer class.
	 *
	 * @param null $exceptions
	 *
	 * @return PHPMailer $mail
	 * @since 0.9.6
	 */
	private function phpmailer( $exceptions = null ) {
		
		// Create Instance
		$mail = new parent;
		// Setup
		$mail->Host       = $this->stored_options->smtp_host;
		$mail->CharSet    = 'utf-8';
		$mail->SMTPAuth   = true;
		$mail->Port       = $this->stored_options->smtp_port;
		$mail->From       = $this->stored_options->from;
		$mail->FromName   = $this->stored_options->fromname;
		$mail->Username   = $this->stored_options->username;
		$mail->Password   = $this->decrypt( $this->stored_options->password );
		$mail->SMTPSecure = $this->stored_options->smtpsecure;
		$mail->isSMTP();
		
		// Debug
		if ( null !== $exceptions ) {
			$mail->SMTPDebug   = 2;
			$mail->Debugoutput = function ( $str, $level ) {
				
				global $debug;
				$debug[] .= "$level: $str\n";
			};
			$mail->Timeout     = 5;
		}
		
		return $mail;
	}
	
	/**
	 * Decrypts the password for the email account stored for replies.
	 *
	 * @param string $string
	 *
	 * @return string $output|bool
	 * @since 0.9.6
	 * @see   \Bonaire\Admin\Includes\Bonaire_Options crypt()
	 */
	private function decrypt( $string ) {
		
		$secret_key = AUTH_KEY;
		$secret_iv  = AUTH_SALT;
		
		if ( '' === $secret_key || '' === $secret_iv ) {
			return $string;
		}
		
		$encrypt_method = 'AES-256-CBC';
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		
		return openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
	
	/**
	 * Returns test mail data.
	 *
	 * @return object $data
	 * @since 0.9.6
	 */
	private function testmail_data() {
		
		$data           = (object) array();
		$data->subject  = __( 'Bonaire Testmail', $this->domain );
		$data->message  = __( 'Howdy. This test message was generated by "Bonaire" for WordPress. Thanks for using this plugin!', $this->domain );
		$data->from     = $this->stored_options->from;
		$data->fromname = $this->stored_options->fromname;
		
		return $data;
	}
	
	/**
	 * @since 1.0.0
	 */
	private function set_email_account_settings_evaluator() {
		
		$this->Bonaire_Account_Settings_Status = new Bonaire_Settings_Status( $this->domain );
	}
	
	/**
	 * Bonaire_Account_Evaluator constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 */
	public function __construct( $domain, $Bonaire_Options ) {
		
		parent::__construct();
		
		$this->domain          = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->stored_options  = $Bonaire_Options->get_stored_options();
		$this->set_email_account_settings_evaluator();
	}
	
	/**
	 * Sets up the mailer instance.
	 *
	 * @param object $data
	 * @param null $exceptions
	 *
	 * @return PHPMailer $mail
	 * @since 0.9.6
	 */
	private function setup( $data, $recipient_email_address, $exceptions = null ) {
		
		$mail = $this->phpmailer( $exceptions );
		$mail->AddAddress( $recipient_email_address );
		$mail->AddReplyTo( $this->stored_options->from );
		$mail->Subject  = strip_tags( $data->subject );
		$mail->Body     = strip_tags( $data->message );
		$mail->From     = $this->stored_options->from;
		$mail->FromName = $data->fromname;
		$mail->isSMTP();
		
		return $mail;
	}
	
	/**
	 * Sends a test mail.
	 *
	 * @param string $recipient_email_address
	 *
	 * @return bool|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function send_testmail( $recipient_email_address ) {
		
		$mail = $this->setup( $this->testmail_data(), $recipient_email_address, null );
		
		try {
			$result = $mail->Send();
		} catch( Exception $e ) {
			
			return new WP_Error( (string) $e->getCode(), (string) $e->getMessage() );
		}
		
		// If sending the test message failed
		if ( false === $result ) {
			$error_message = __( 'Sending test message failed:', $this->domain ) . ' ' . __( 'Could not reach the mail server.', $this->domain ) . '<br>' . __( 'Please make sure that you are connected to the internet, and that you\'ve tested the SMTP and IMAP settings with the respective buttons on this plugin\'s settings page.', $this->domain );
			
			return new WP_Error( - 2, $error_message );
		}
		
		return $result;
	}
	
	/**
	 * Returns an instance of the mailer class.
	 *
	 * @param null $exceptions
	 *
	 * @return \PHPMailer
	 * @since 0.9.6
	 */
	private function get_phpmailer( $exceptions = null ) {
		
		return $this->phpmailer( $exceptions );
	}
	
	/**
	 * Calls the method that evaluates the SMTP settings.
	 *
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_test_smtp_settings() {
		
		return $this->test_smtp_settings();
	}
	
	/**
	 * Check if the form is filled out completely. If so, evaluate the SMTP settings.
	 * Sets the 'settings status' after evaluation.
	 *
	 * @param bool $internal
	 *
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	private function test_smtp_settings() {

		if ( true === $this->meets_requirements( 'smtp' ) ) {
			
			$result = $this->evaluate_smtp_settings();
			if ( is_wp_error( $result ) ) {
				$status     = 'orange';
				$error_code = $result->get_error_code();
				$error_code = isset( $error_code ) ? $error_code : false;
				$result     = array(
					'success' => false,
					'message' => $result->get_error_message(),
					'messages' => false,
					'error_code' => $error_code
				);
				
				$this->Bonaire_Account_Settings_Status->set_settings_status( 'smtp', $status );
				
				return $this->create_response( $result, $status );
			}
			
			$status = $this->Bonaire_Account_Settings_Status->get_settings_status( 'smtp' );
			if ( true === $result['success'] ) {
				$this->Bonaire_Account_Settings_Status->set_settings_status( 'smtp', 'green' );
			} else {
				$status = 'orange';
				$this->Bonaire_Account_Settings_Status->set_settings_status( 'smtp', 'orange' );
			}
			
			return $this->create_response( $result, $status );
		}
		
		$this->Bonaire_Account_Settings_Status->set_settings_status( 'smtp', 'orange' );
		
		return new WP_Error( 1, __( 'Please add your email account settings first.', $this->domain ) );
	}
	
	/**
	 * Calls the function that evaluates the IMAP settings.
	 *
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_test_imap_settings() {
		
		return $this->test_imap_settings();
	}
	
	/**
	 * Runs a series of methods to evaluate the IMAP settings.
	 *
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	private function test_imap_settings() {
		
		if ( true === $this->meets_requirements( 'imap' ) && true === $this->meets_requirements( 'smtp' ) ) {
			
			$result = $this->evaluate_imap_settings();
			if ( is_wp_error( $result ) ) {
				$status     = 'orange';
				$error_code = $result->get_error_code();
				$error_code = isset( $error_code ) ? $error_code : false;
				$result     = array(
					'success' => false,
					'message' => $result->get_error_message(),
					'messages' => false,
					'error_code' => $error_code
				);
				$this->Bonaire_Account_Settings_Status->set_settings_status( 'imap', $status );
				
				return $this->create_response( $result, $status );
			}
			
			if ( true === $result['success'] ) {
				$status = 'green';
				$this->Bonaire_Account_Settings_Status->set_settings_status( 'imap', $status );
			} else {
				$status = 'orange';
				$this->Bonaire_Account_Settings_Status->set_settings_status( 'imap', $status );
			}
			
			return $this->create_response( $result, $status );
		}
		
		if ( 'yes' === $this->stored_options->{0}->save_reply ) {
			$this->Bonaire_Account_Settings_Status->set_settings_status( 'imap', 'orange' );
		} else {
			$this->Bonaire_Account_Settings_Status->set_settings_status( 'imap', 'inactive' );
		}
		
		return new WP_Error( 1, __( 'Please add your email account settings first.', $this->domain ) );
	}
	
	/**
	 * Checks if there are no empty input fields on the settings page.
	 *
	 * @param string $protocol
	 *
	 * @return bool
	 * @since 0.9.6
	 */
	private function meets_requirements( $protocol ) {
		
		$is_complete = true;
		foreach ( (array) $this->options_meta as $key => $args ) {
			if ( ( isset( $this->stored_options->{$key} ) && '' === $this->stored_options->{$key} ) && $protocol === $args['group'] ) {
				if ( $key !== 'inbox_folder_path' ) {
					$is_complete = false;
				}
			}
		}
		
		return $is_complete;
	}
	
	/**
	 * Calls the method that evaluates the SMTP settings.
	 *
	 * @return bool|array|WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function evaluate_smtp_settings() {
		
		$stored_options = $this->Bonaire_Options->get_stored_options();
		$smtp_host      = $stored_options->smtp_host;
		$smtp_port      = $stored_options->smtp_port;
		$smtp_ports     = array( $stored_options->smtp_port );
		
		return $this->run_smtp_evaluation( $smtp_host, $smtp_port, $smtp_ports );
	}
	
	/**
	 * Runs a series of methods to evaluate the SMTP settings.
	 *
	 * @param string $smtp_host
	 * @param int $smtp_port
	 * @param array $smtp_ports
	 *
	 * @return array|WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	private function run_smtp_evaluation( $smtp_host, $smtp_port, $smtp_ports ) {
		
		$response = null;
		$messages = false;
		
		// Check Internet Connection
		$connection_result = $this->is_connected();
		if ( is_wp_error( $connection_result ) ) {
			$error_code = $connection_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $connection_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully checked internet connection.', $this->domain );
		
		//Resolve SMTP hostname
		$resolve_result = $this->resolve_smtp_hostname( $smtp_host );
		if ( is_wp_error( $resolve_result ) ) {
			$error_code = $resolve_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $resolve_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully resolved host name.', $this->domain );
		
		// Test SMTP port
		$port_result = $this->test_smtp_port( $smtp_host, $smtp_ports );
		if ( is_wp_error( $port_result ) ) {
			$error_code = $port_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $port_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully tested SMTP port.', $this->domain );
		
		// Test SMTPSecure
		$smtpsecure_result = $this->test_smtpsecure();
		if ( is_wp_error( $smtpsecure_result ) ) {
			$error_code = $smtpsecure_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $smtpsecure_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully tested SMTPSecure setting.', $this->domain );
		
		// Test SMTP user credentials
		$socket             = fsockopen( $smtp_host, $smtp_port, $errno, $errstr, 2 );
		$credentials_result = $this->test_credentials();
		fclose( $socket );
		if ( is_wp_error( $credentials_result ) ) {
			$error_code = $credentials_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $credentials_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully authenticated to the email account via SMTP.', $this->domain );
		
		// Add final success message.
		$messages[] = '<strong>' . __( 'Your SMTP settings are valid.', $this->domain ) . '</strong>';
		
		$result = array( 'success' => true, 'message' => false, 'messages' => $messages, 'error_code' => 0 );
		
		return $this->create_response( $result, 'green' );
	}
	
	/**
	 * Calls the method that evaluates the IMAP settings.
	 *
	 * @return array|WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function evaluate_imap_settings() {
		
		return $this->run_imap_evaluation();
	}
	
	/**
	 * Evaluates the IMAP settings.
	 *
	 * @return array|WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	private function run_imap_evaluation() {
		
		$imap_host  = $this->stored_options->imap_host;
		$imap_ports = array( $this->stored_options->imap_port );
		
		$wp_error = null;
		$messages = false;
		$response = null;
		
		// Check Internet Connection
		$connection_result = $this->is_connected();
		if ( is_wp_error( $connection_result ) ) {
			$error_code = $connection_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $connection_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully checked internet connection.', $this->domain );
		
		//Check for IMAP extension
		$extension_result = $this->is_imap_extension_loaded();
		if ( is_wp_error( $extension_result ) ) {
			$error_code = $extension_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $extension_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully checked for IMAP extension.', $this->domain );
		
		//Resolve IMAP hostname
		$resolve_result = $this->resolve_imap_hostname( $imap_host );
		if ( is_wp_error( $resolve_result ) ) {
			$error_code = $resolve_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $resolve_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully resolved host name.', $this->domain );
		
		// Test IMAP port
		$port_result = $this->test_imap_port( $imap_host, $imap_ports );
		if ( is_wp_error( $port_result ) ) {
			$error_code = $port_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $port_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully tested IMAP port.', $this->domain );
		
		// Test SMTPSecure
		$smtpsecure_result = $this->test_smtpsecure(true);
		if ( is_wp_error( $smtpsecure_result ) ) {
			$error_code = $smtpsecure_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $smtpsecure_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully tested SMTPSecure setting.', $this->domain );
		
		// Test SSL
		if ( 'yes' === $this->stored_options->save_reply && 'cert' === $this->stored_options->ssl_certification_validation ) {
			
			$ssl_result = $this->is_ssl();
			if ( is_wp_error( $ssl_result ) ) {
				$error_code = $ssl_result->get_error_code();
				$error_code = isset( $error_code ) ? $error_code : false;
				$result     = array(
					'success' => false,
					'message' => $ssl_result->get_error_message(),
					'messages' => $messages,
					'error_code' => $error_code
				);
				
				return $this->create_response( $result, 'orange' );
			}
		}
		$messages[] = __( 'Successfully tested IMAP port.', $this->domain );
		
		// Test INBOX.Sent folder
		$folder_result = $this->test_inbox();
		if ( is_wp_error( $folder_result ) ) {
			$error_code = $folder_result->get_error_code();
			$error_code = isset( $error_code ) ? $error_code : false;
			$result     = array(
				'success' => false,
				'message' => $folder_result->get_error_message(),
				'messages' => $messages,
				'error_code' => $error_code
			);
			
			return $this->create_response( $result, 'orange' );
		}
		$messages[] = __( 'Successfully contacted the inbox folder on the mail server.', $this->domain );
		
		// Add final success message.
		$messages[] = '<strong>' . __( 'Your IMAP settings are valid.', $this->domain ) . '</strong>';
		
		$result = array( 'success' => true, 'message' => false, 'messages' => $messages, 'error_code' => 0 );
		
		return $this->create_response( $result, 'green' );
	}
	
	/**
	 * @param array $result
	 * @param string $status
	 *
	 * @return array|WP_Error
	 */
	private function create_response( $result, $status ) {
		
		$string = '';
		if ( isset( $result['messages'] ) && false !== $result['messages'] ) {
			foreach ( $result['messages'] as $i => $message ) {
				$string .= $message . '<br>';
			}
			$message = $string . '<br>' . $result['message'];
		} else {
			$message = $result['message'];
		}
		
		if ( true === $result['success'] ) {
			
			return array( 'success' => true, 'message' => $message, 'status' => $status );
		}
		
		return new WP_Error( $result['error_code'], $message, $status );
	}
	
	/**
	 * @return bool|WP_Error
	 */
	private function is_connected() {
		
		try {
			$connection = @fsockopen( "www.google.com", 80, $errno, $errstr, 2 );
			if ( $connection ) {
				$result = true;
				fclose( $connection );
				flush();
			} else {
				flush();
				$error_message = '<strong>' . __( 'It seems that you are not connected to the internet, or a firewall is blocking access to it.', $this->domain ) . '</strong><br>' . __( 'Please make sure that you are connected to the internet.', $this->domain );
				$result        = new WP_Error( 1, $error_message );
			}
			unset( $connection );
			
			return $result;
		} catch( Exception $error ) {
			$error_message = '<strong>' . __( 'Internal Error: Unable to check the SMTP port.', $this->domain ) . '</strong><br>' . __( 'Please try again later.', $this->domain );
			
			return new WP_Error( 2, $error_message );
		}
	}
	
	/**
	 * Resolves the SMTP host name.
	 *
	 * @param string $smtp_host
	 *
	 * @return bool|\WP_Error
	 * @since 0.9.6
	 */
	private function resolve_smtp_hostname( $smtp_host ) {
		
		try {
			$test_dns = gethostbyname( $smtp_host . '.' );
			
			if ( ! preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				$error_message = '<strong>' . printf( __( 'Failed to resolve SMTP host name (%d).', $this->domain ), $smtp_host ) . '</strong><br>' . __( 'Please review your settings and run the test again.', $this->domain );
				
				return new WP_Error( 1, $error_message );
			}
		} catch( Exception $e ) {
			$error_message = '<strong>' . printf( __( 'Internal Error: Unable to resolve SMTP host name.', $this->domain ), $smtp_host ) . '</strong><br>' . __( 'Please review your settings and run the test again.', $this->domain );
			
			return new WP_Error( 2, $error_message );
		}
		
		return true;
	}
	
	private function test_smtpsecure($is_imap = false) {
		
		$mail = $this->get_phpmailer( true );
		
		if($is_imap){
			
			try {
				$ssl_certification_validation = 'nocert' === $this->stored_options->ssl_certification_validation ? 'novalidate-cert' : '';
				$mailbox                      = $this->get_mailbox( $mail, $ssl_certification_validation, true );
				
				// Check IMAP connection
				$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or false;
				if ( false !== $imapStream ) {
					// Close connection
					imap_close( $imapStream );
					
					return true;
				}
				
				// Check IMAP connection
				$mailbox = $this->get_mailbox( $mail, $ssl_certification_validation, true, true );
				$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or false;
				if ( false !== $imapStream ) {
					// Close connection
					imap_close( $imapStream );
					$imap_secure   = 'ssl' === $this->stored_options->imapsecure ? 'TLS' : 'SSL';
					$error_message = sprintf( __( 'It seems you shoud set the value for IMAPSecure to "%s". Please change the settings and save them, before running the test again.', $this->domain ), $imap_secure );
					
					return new WP_Error( 1, $error_message );
				}
				
				$error_message = __( 'Failed to check IMAPSecure settings. You may want to review your settings and try again.', $this->domain );
				
				return new WP_Error( 1, $error_message );
			} catch( Exception $e ) {
				
				return new WP_Error( 2, __( 'Internal Error: Unable to check IMAPSecure settings.', $this->domain ) . '<br>' . __( 'Please try again later.', $this->domain ) );
			}
		}
		
		try {
			
			$ssl_certification_validation = 'nocert' === $this->stored_options->ssl_certification_validation ? 'novalidate-cert' : '';
			$mailbox                      = $this->get_mailbox( $mail, $ssl_certification_validation );
			
			// Check SMTP connection
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or false;
			if ( false !== $imapStream ) {
				// Close connection
				imap_close( $imapStream );
				
				return true;
			}
			
			// Check IMAP connection
			$mailbox = $this->get_mailbox( $mail, $ssl_certification_validation,false, true );
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or false;
			if ( false !== $imapStream ) {
				// Close connection
				imap_close( $imapStream );
				$smtp_secure   = 'ssl' === $this->stored_options->smtpsecure ? 'TLS' : 'SSL';
				$error_message = sprintf( __( 'It seems you shoud set the value for SMTPSecure to "%s". Please change the settings and save them, before running the test again.', $this->domain), $smtp_secure );
				return new WP_Error( 1, $error_message );
			}

			$error_message = __('Failed to check SMTPSecure settings. You may want to review your settings and try again.', $this->domain );
			
			return new WP_Error( 1, $error_message );
			
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to check SMTPSecure settings.', $this->domain ) . '<br>' . __( 'Please try again later.', $this->domain ) );
		}
	}
	
	/**
	 * Tests the SMTP port.
	 *
	 * @param string $smtp_host
	 * @param array $smtp_ports
	 *
	 * @return bool|\WP_Error
	 * @since 0.9.6
	 */
	private function test_smtp_port( $smtp_host, $smtp_ports ) {
		
		try {
			if ( $socket = fsockopen( $smtp_host, $smtp_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$error_message = '<strong>' . __( 'The SMTP port number seems to be wrong.', $this->domain ) . '</strong><br>' . __( 'Please review your setting.', $this->domain );
				$result        = new WP_Error( 1, $error_message );
			}
			unset( $socket );
		} catch( Exception $error ) {
			$error_message = __( 'Internal Error: Unable to check the SMTP port.', $this->domain ) . '<br>' . __( 'Please try again later.', $this->domain );
			
			return new WP_Error( 2, $error_message );
		}
		
		return $result;
	}
	
	/**
	 * Tests the SMTP user credentials and settings.
	 *
	 * @return bool|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	private function test_credentials() {
		
		try {
			parent::SmtpConnect();
		} catch( Exception $error ) {
			$error_message = '<strong>' . __( 'SMTP Error: Could not authenticate.', $this->domain ) . '</strong><br>' . __( 'Please review your username and password.', $this->domain );
			
			return new WP_Error( 1, $error_message );
		}
		
		return true;
	}
	
	/**
	 * Checks if the PHP IMAP extension is loaded.
	 *
	 * @return bool|\WP_Error
	 * @since 1.0.0
	 */
	private function is_imap_extension_loaded() {
		
		$error_message  = '<strong>' . __( 'PHP IMAP extension is missing.', $this->domain ) . '</strong><br>' . __( 'Bonaire needs this extension to be installed and loaded in order to handle IMAP events. For local development, see for example: ', $this->domain );
		$read_more_link = '<a href="https://stackoverflow.com/questions/9654453/fatal-error-call-to-undefined-function-imap-open-in-php" target="_blank">Fatal error: Call to undefined function imap_open() in PHP</a>';
		
		$error_string = $error_message . ' ' . $read_more_link . '<br>' . __( 'For live websites, contact your admin or host in order to include this PHP extension.', $this->domain );
		
		return extension_loaded( "imap" ) ? true : new WP_Error( 2, $error_string );
	}
	
	/**
	 * Checks if SSL is enabled.
	 *
	 * @return bool|WP_Error
	 * @since 1.0.0
	 */
	public function is_ssl() {
		
		try{
			if ( isset( $_SERVER['HTTPS'] ) ) {
				if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) {
					return true;
				}
				if ( '1' == $_SERVER['HTTPS'] ) {
					return true;
				}
			} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
				return true;
			}
			
			return new WP_Error( - 31, __( 'You need to install a SSL certificate, or set the value for "use certification validation to "nocert" (read it\'s tooltip and use the "nocert" option during local development only).', $this->domain ), 'orange' );
		} catch(Exception $e) {
			
			return new WP_Error( - 31, __( 'You need to install a SSL certificate, or set the value for "use certification validation to "nocert" (read it\'s tooltip and use the "nocert" option during local development only).', $this->domain ), 'orange' );
		}
		
	}
	
	/**
	 * Resloves the IMAP host.
	 *
	 * @param string $imap_host
	 *
	 * @return bool|\WP_Error
	 * @since 0.9.6
	 */
	private function resolve_imap_hostname( $imap_host ) {
		
		try {
			$test_dns = gethostbyname( $imap_host . '.' );
			
			if ( ! preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				$error_message = '<strong>' . printf( __( 'Failed to resolve IMAP host (%d).', $this->domain ), $imap_host ) . '</strong><br>' . __( 'Please review your settings and run the test again.', $this->domain );
				
				return new WP_Error( 1, $error_message );
			}
		} catch( Exception $e ) {
			$error_message = '<strong>' . printf( __( 'Internal Error: Unable to resolve IMAP hostname.', $this->domain ), $imap_host ) . '</strong><br>' . __( 'Please try again later.', $this->domain );
			
			return new WP_Error( 2, $error_message );
		}
		
		return true;
	}
	
	/**
	 * Tests the IMAP port.
	 *
	 * @param string $smtp_host
	 * @param array $smtp_ports
	 *
	 * @return bool|\WP_Error
	 * @since 0.9.6 .
	 */
	private function test_imap_port( $smtp_host, $smtp_ports ) {
		
		try {
			
			if ( $socket = fsockopen( $smtp_host, $smtp_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$error_message = '<strong>' . __( 'Failed to evaluate port number.', $this->domain ) . '</strong><br>' . printf( esc_html__( 'Please review the port number of your IMAP server (%d).', $this->domain ), $errstr );
				$result        = new WP_Error( 1, $error_message );
			}
			unset( $socket );
		} catch( Exception $error ) {
			$error_message = __( 'Internal Error: Unable to check IMAP port.', $this->domain ) . '<br>' . __( 'Please try again later.', $this->domain );
			
			return new WP_Error( 2, $error_message );
		}
		
		return $result;
	}
	
	/**
	 * Tests wether the inbox is reachable or not and returns true on success or
	 * it returns an error.
	 *
	 * @return bool|\WP_Error
	 * @since 0.9.6
	 */
	private function test_inbox() {
		
		// Check for SSL
		if ( 'cert' === $this->stored_options->ssl_certification_validation && false === $this->is_ssl() ) {
			$error_message = __( 'Error: No SSL Certificate installed on this website.<br>During local website development, use the \'nocert\' option.<br>nIf you are on a live website, consider installing a SSL certificate and then use the \'cert\' option. Otherwise, you\'re a possible subject of man in the middle attacks', $this->domain ) . '<br>' . __( 'Please review your settings and run the test again.', $this->domain );
			$read_morelink = printf( '<a href="https://stackoverflow.com/questions/7891729/certificate-error-using-imap-in-php" target="_blank">%d</a>)', __( 'Read more', $this->domain ) );
			
			$error_string = $error_message . '(' . $read_morelink . ')';
			
			return new WP_Error( 1, esc_html($error_string) );
		}
		
		$mail = $this->get_phpmailer( true );
		
		try {
			$ssl_certification_validation = 'nocert' === $this->stored_options->ssl_certification_validation ? 'novalidate-cert' : '';
			$mailbox                      = $this->get_mailbox( $mail, $ssl_certification_validation );
			
			// Check IMAP connection
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or false;
			// Retrieve folder list
			$list = imap_list( $imapStream, '{' . $this->stored_options->imap_host . '}', '*' );
			// Check Sent Items Folder Path
			$path = $this->get_sent_items_folder_path_for_testing( $mail );
			if(false !== $imapStream && in_array( $path, $list, true ) ){
				// Close connection
				imap_close( $imapStream );
				
				return true;
			}

			$imap_errors = imap_errors();
			$error_message = sprintf( __( 'It seems you shoud set the value for SMTPSecure to "%d".', $this->domain), $imap_errors  ) . '<br>' . __('Please review your settings and run the test again.', $this->domain);
			
			return new WP_Error( 1, $error_message );

		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to search for Folder.', $this->domain ) . '<br>' . __( 'Please try again later.', $this->domain ) );
		}

	}
	
	/**
	 * Returns the path to the mailbox on the mail server.
	 *
	 * @param $mail
	 * @param $ssl_certification_validation
	 *
	 * @return string
	 */
	private function get_mailbox( $mail, $ssl_certification_validation, $is_imap = false, $recheck = false ) {
		
		$mail->Host       = $this->stored_options->imap_host;
		$mail->Port       = $this->stored_options->imap_port;
		$secure = $is_imap ? $this->stored_options->imapsecure : $this->stored_options->smtpsecure;
		$smtpsecure = $recheck ? 'ssl' === $this->stored_options->imapsecure ? 'tls' : 'ssl' : $secure;
		
		if ( $this->is_gmail() ) {

			$mailserver_path = '{' . $mail->Host . ':' . $mail->Port . '/imap/' . $smtpsecure . '/' . $ssl_certification_validation . '}';
			
			return $mailserver_path . 'INBOX';
		}
		
		$mailserver_path = '{' . $mail->Host . ':' . $mail->Port . '/imap/' . $smtpsecure . $ssl_certification_validation . '}';
		
		return $mailserver_path . 'INBOX';
	}
	
	/**
	 * Returns the path to the user's Sent Items folder.
	 *
	 * @param $mail
	 *
	 * @return string
	 */
	private function get_sent_items_folder_for_send_mail( $mail ) {
		
		$inbox = $this->is_gmail() ? "[Gmail]/" : "INBOX.";
		
		$inbox_folder_name = $this->is_gmail() && '' !== $this->stored_options->inbox_folder_name ? $this->stored_options->inbox_folder_name : 'Sent';
		
		return '{' . $mail->Host . '}' . $inbox . $inbox_folder_name;
	}
	
	/**
	 * @param $mail
	 *
	 * @return string
	 */
	private function get_sent_items_folder_path_for_testing( $mail ) {
		
		if ( $this->is_gmail() && '' !== $this->stored_options->inbox_folder_path ) {
			
			return $this->stored_options->inbox_folder_path;
		}
		
		return $this->get_sent_items_folder_for_send_mail( $mail );
	}
	
	private function is_gmail() {
		
		return preg_match( '/smtp.gmail.com/', $this->stored_options->smtp_host );
	}
	
}