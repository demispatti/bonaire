<?php

namespace Bonaire\Admin\Includes;

use JJG;
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
if ( ! class_exists( 'Ping' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ping.php';
}

/**
 * The class responsible for evaluating / testing the email account settings.
 *
 * @since             0.9.0
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Settings_Evaluator {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.0
	 * @access   public static
	 */
	public static $domain;
	
	/**
	 * Holds the stored options.
	 *
	 * @var object $stored_options
	 * @since    0.9.0
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Holds the options meta data.
	 *
	 * @var object $options_meta
	 * @since    0.9.0
	 * @access   private
	 */
	private $options_meta;
	
	/**
	 * Holds the instance of the class responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.0
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Holds the mailer instance.
	 *
	 * @var PHPMailer $PHPMailer
	 * @since    0.9.0
	 * @access   private
	 */
	private $PHPMailer;
	
	/**
	 * Bonaire_Settings_Evaluator constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @param AdminIncludes\Bonaire_Mail $Bonaire_Mail
	 *
	 * @since 0.9.0
	 * @return void
	 */
	public function __construct( $domain, $Bonaire_Options, $Bonaire_Mail ) {
		
		self::$domain = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->stored_options = $Bonaire_Options->get_stored_options( '0' );
		$this->options_meta = $Bonaire_Options->get_options_meta();
		$this->PHPMailer = $Bonaire_Mail->get_phpmailer( true );
	}
	
	/**
	 * Calls the method that evaluates the SMTP settings.
	 *
	 * @since 0.9.0
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 */
	public function bonaire_test_smtp_settings() {
		
		return $this->test_smtp_settings();
	}
	
	/**
	 * Check if the form is filled out completely. If so, evaluate the SMTP settings.
	 * Sets the 'settings state' after evaluation.
	 *
	 * @param bool $internal
	 *
	 * @since 0.9.0
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 */
	private function test_smtp_settings( $internal = false ) {
		
		if ( true === $this->meets_requirements( 'smtp' ) ) {
			$result = $this->evaluate_smtp_settings();
			if ( false !== $internal ) {
				
				if ( isset( $result['success'][4] ) ) {
					
					$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'smtp', 'green' );
					
					return true;
				}
				
				return false;
			}
			
			$state = 'green';
			if ( isset( $result['success'][4] ) ) {
				$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'smtp', 'green' );
			} else {
				$state = 'orange';
				$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'smtp', 'orange' );
			}
			
			return $this->create_response( $result, $state );
		}
		
		$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'smtp', 'red' );
		
		return new \WP_Error( 1, __( 'Please add your email account settings first.', self::$domain ) );
	}
	
	/**
	 * Calls the function that evaluates the IMAP settings.
	 *
	 * @since 0.9.0
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 */
	public function bonaire_test_imap_settings() {
		
		return $this->test_imap_settings();
	}
	
	/**
	 * Runs a series of methods to evaluate the IMAP settings.
	 *
	 * @param bool $internal
	 *
	 * @since 0.9.0
	 * @return bool|array|\WP_Error
	 * @throws \Exception
	 */
	private function test_imap_settings( $internal = false ) {
		
		if ( true === $this->meets_requirements( 'imap' ) && true === $this->meets_requirements( 'smtp' ) ) {
			$result = $this->evaluate_imap_settings();
			if ( false !== $internal ) {
				
				if ( isset( $result['success'][4] ) ) {
					
					$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'imap', 'green' );
					
					return true;
				}
				
				return false;
			}
			
			$state = 'green';
			if ( isset( $result['success'][4] ) ) {
				$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'imap', 'green' );
			} else {
				$state = 'orange';
				$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'imap', 'orange' );
			}
			
			return $this->create_response( $result, $state );
		}
		
		$this->Bonaire_Options->bonaire_set_evaluation_state( $protocol = 'imap', 'red' );
		
		return new \WP_Error( 1, __( 'Please add your email account settings first.', self::$domain ) );
	}
	
	/**
	 * Checks if there are no empty input fields on the settings page.
	 *
	 * @param string $protocol
	 *
	 * @since 0.9.0
	 * @return bool
	 */
	private function meets_requirements( $protocol ) {
		
		$is_complete = true;
		foreach ( (array) $this->options_meta as $key => $args ) {
			if ( ( isset( $this->stored_options->{$key} ) && '' === $this->stored_options->{$key} ) && $protocol === $args['group'] ) {
				$is_complete = false;
			}
		}
		
		return $is_complete;
	}
	
	/**
	 * Calls the method that evaluates the SMTP settings.
	 *
	 * @since 0.9.0
	 * @return bool|array
	 * @throws \Exception
	 */
	private function evaluate_smtp_settings() {
		
		$smtp_host = $this->stored_options->smtp_host;
		$smtp_port = $this->stored_options->smtp_port;
		$smtp_ports = array( $this->stored_options->smtp_port );
		
		return $this->run_smtp_evaluation( $smtp_host, $smtp_port, $smtp_ports );
	}
	
	/**
	 * Runs a series of methods to evaluate the SMTP settings.
	 *
	 * @param  string $smtp_host
	 * @param  int $smtp_port
	 * @param  array $smtp_ports
	 *
	 * @since 0.9.0
	 * @return array
	 * @throws \Exception
	 */
	private function run_smtp_evaluation( $smtp_host, $smtp_port, $smtp_ports ) {
		
		$response = null;
		$successes = null;
		
		//Resolve SMTP hostname
		$resolve_result = $this->resolve_smtp_hostname( $smtp_host );
		if ( is_wp_error( $resolve_result ) ) {
			
			return array( 'success' => $successes, 'error' => $resolve_result->get_error_message() );
		}
		$successes[0] = __( 'Successfully resolved host name.', self::$domain );
		
		// Ping SMTP host
		$ping_result = $this->ping_smtp_host( $smtp_host );
		if ( is_wp_error( $ping_result ) ) {
			
			return array( 'success' => $successes, 'error' => $ping_result->get_error_message() );
		}
		$successes[1] = __( 'Successfully pinged host.', self::$domain );
		
		// Test SMTP port
		$port_result = $this->test_smtp_port( $smtp_host, $smtp_ports );
		if ( is_wp_error( $port_result ) ) {
			
			return array( 'success' => $successes, 'error' => $port_result->get_error_message() );
		}
		$successes[2] = __( 'Successfully tested SMTP port.', self::$domain );
		
		// Test SMTP user credentials
		$socket = fsockopen( $smtp_host, $smtp_port, $errno, $errstr, 2 );
		$credentials_result = $this->test_credentials();
		fclose( $socket );
		if ( is_wp_error( $credentials_result ) ) {
			
			return array( 'success' => $successes, 'error' => $credentials_result->get_error_message() );
		}
		$successes[3] = __( 'Successfully authenticated to the account via SMTP.', self::$domain );
		
		// Add final success message.
		$successes[4] = __( 'Successfully evaluated your SMTP settings.', self::$domain );
		
		return array( 'success' => $successes, 'error' => false );
	}
	
	/**
	 * Calls the method that evaluates the IMAP settings.
	 *
	 * @since 0.9.0
	 * @return array
	 * @throws \Exception
	 */
	private function evaluate_imap_settings() {
		
		return $this->run_imap_evaluation();
	}
	
	/**
	 * Evaluates the IMAP settings.
	 *
	 * @since 0.9.0
	 * @return array
	 * @throws \Exception
	 */
	private function run_imap_evaluation() {
		
		$imap_host = $this->stored_options->imap_host;
		$imap_ports = array( $this->stored_options->imap_port );
		
		$wp_error = null;
		$successes = array();
		$response = null;
		
		//Resolve IMAP hostname
		$resolve_result = $this->resolve_imap_hostname( $imap_host );
		if ( is_wp_error( $resolve_result ) ) {
			
			return array( 'success' => $successes, 'error' => $resolve_result->get_error_message() );
		}
		$successes[0] = __( 'Successfully resolved host name.', self::$domain );
		
		// Ping IMAP host
		$ping_result = $this->ping_imap_host( $imap_host );
		if ( is_wp_error( $ping_result ) ) {
			
			return array( 'success' => $successes, 'error' => $ping_result->get_error_message() );
		}
		$successes[1] = __( 'Successfully pinged host.', self::$domain );
		
		// Test IMAP port
		$port_result = $this->test_imap_port( $imap_host, $imap_ports );
		if ( is_wp_error( $port_result ) ) {
			
			return array( 'success' => $successes, 'error' => $port_result->get_error_message() );
		}
		$successes[2] = __( 'Successfully tested IMAP port.', self::$domain );
		
		// Test INBOX.Sent folder
		$folder_result = $this->test_inbox();
		if ( is_wp_error( $folder_result ) ) {
			
			return array( 'success' => $successes, 'error' => $folder_result->get_error_message() );
		}
		$successes[3] = __( 'Successfully contacted INBOX.Sent folder.', self::$domain );
		
		// Add final success message.
		$successes[4] = __( 'Successfully evaluated your settings.', self::$domain );
		
		return array( 'success' => $successes, 'error' => false );
	}
	
	/**
	 * @param array $result
	 * @param string $state
	 *
	 * @return array|\WP_Error
	 */
	private function create_response( $result, $state ) {
		
		$string = '';
		if ( false !== $result['error'] ) {
			
			return new \WP_Error( 1, $result['error'] );
		}
		
		$count = count( $result['success'] );
		foreach ( $result['success'] as $i => $message ) {
			
			if ( ( $i + 1 ) === $count ) {
				$string .= '<br><strong>' . $message . '<strong>';
			} else {
				$string .= $message . '<br>';
			}
		}
		
		return array( 'message' => $string, 'state' => $state );
	}
	
	/**
	 * Resolves the SMTP host name.
	 *
	 * @param string $smtp_host
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 */
	private function resolve_smtp_hostname( $smtp_host ) {
		
		try {
			$test_dns = gethostbyname( $smtp_host . '.' );
			
			if ( ! preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				
				return new \WP_Error( 1, __( 'Failed to resolve SMTP hostname (' . $smtp_host . ') - you appear to have a DNS resolution issue with your mail server.', self::$domain ) );
			}
		} catch( Exception $e ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to resolve SMTP hostname.', self::$domain ) );
		}
		
		return true;
	}
	
	/**
	 * Pings the SMTP host.
	 *
	 * @param $smtp_host
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 * @throws \Exception
	 */
	private function ping_smtp_host( $smtp_host ) {
		
		try {
			$ping = new JJG\Ping( $smtp_host );
			$latency = $ping->ping();
			if ( $latency === false ) {
				
				return new \WP_Error( 1, __( 'Failed to ping host (' . $smtp_host . '). Please review the settings for your SMTP host.', self::$domain ) );
			}
		} catch( Exception $e ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to ping SMTP host.', self::$domain ) );
		}
		
		return true;
	}
	
	/**
	 * Tests the SMTP port.
	 *
	 * @param string $smtp_host
	 * @param array $smtp_ports
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 */
	private function test_smtp_port( $smtp_host, $smtp_ports ) {
		
		try {
			if ( $socket = fsockopen( $smtp_host, $smtp_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$result = new \WP_Error( 1, __( 'The port number is wrong. Please review the port number of your SMTP server. (' . $errstr . ')', self::$domain ) );
			}
			unset( $socket );
		} catch( Exception $error ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to check SMTP port.', self::$domain ) );
		}
		
		return $result;
	}
	
	/**
	 * Tests the SMTP user credentials and settings.
	 *
	 * @since 0.9.0
	 * @return \WP_Error|bool
	 */
	private function test_credentials() {
		
		try {
			$this->PHPMailer->SmtpConnect();
		} catch( Exception $error ) {
			
			return new \WP_Error( 1, __( 'SMTP Error: Could not authenticate.', self::$domain ) . ' ' . __( 'Please review your username and password.', self::$domain ) );
		}
		
		return true;
	}
	
	/**
	 * Resloves the IMAP host.
	 *
	 * @param string $smtp_host
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 */
	private function resolve_imap_hostname( $smtp_host ) {
		
		try {
			$test_dns = gethostbyname( $smtp_host . '.' );
			
			if ( ! preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				
				return new \WP_Error( 1, __( 'Failed to resolve IMAP hostname (' . $smtp_host . ') - you appear to have a DNS resolution issue with your mail server.', self::$domain ) );
			}
		} catch( Exception $e ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to resolve IMAP hostname.', self::$domain ) );
		}
		
		return true;
	}
	
	/**
	 * Pings the SMTP host.
	 *
	 * @param string $smtp_host
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 * @throws \Exception
	 */
	private function ping_imap_host( $smtp_host ) {
		
		try {
			$ping = new JJG\Ping( $smtp_host );
			$latency = $ping->ping();
			if ( $latency !== false ) {
				
				return true;
			}
			
			return new \WP_Error( 1, __( 'Failed to ping host (' . $smtp_host . '). Please review the settings for your IMAP host.', self::$domain ) );
		} catch( Exception $e ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to ping IMAP host.', self::$domain ) );
		}
	}
	
	/**
	 * Tests the IMAP port.
	 *
	 * @param string $smtp_host
	 * @param array $smtp_ports
	 *
	 * @since 0.9.0.
	 * @return bool|\WP_Error
	 */
	private function test_imap_port( $smtp_host, $smtp_ports ) {
		
		try {
			
			if ( $socket = fsockopen( $smtp_host, $smtp_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$result = new \WP_Error( 1, __( 'Failed to evaluate port number. Please review the port number of your IMAP server. (' . $errstr . ')', self::$domain ) );
			}
			unset( $socket );
		} catch( Exception $error ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to check IMAP port.', self::$domain ) );
		}
		
		return $result;
	}
	
	/**
	 * Tests wether the inbox is reachable or not and returns true on success or
	 * it returns an error.
	 *
	 * @since 0.9.0
	 * @return bool|\WP_Error
	 */
	private function test_inbox() {
		
		$mail = $this->PHPMailer;
		
		try {
			$mail->Host = $this->stored_options->imap_host;
			$mail->Port = $this->stored_options->imap_port;
			$mail->SMTPSecure = $this->stored_options->imapsecure;
			
			$use_ssl_certification_validation = '';
			if ( 'nocert' === $this->stored_options->use_ssl_certification_validation ) {
				$use_ssl_certification_validation = '/novalidate-cert/norsh/service=imap/user=' . $mail->Username;
			}
			$mailbox = '{' . $mail->Host . '/' . $mail->SMTPSecure . $use_ssl_certification_validation . '}';
			$path = '{' . $this->stored_options->imap_host . '}' . 'INBOX.Sent';
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password );
			$list = imap_list( $imapStream, '{' . $this->stored_options->imap_host . '}', '*' );
			imap_close( $imapStream );
			$imap_errors = imap_errors();
			
			if ( false === $imapStream && is_array( $imap_errors ) ) {
				$error_message = false !== $imapStream ? __( 'Failed to connect to host (connection timeout). Please review your settings and run test again.', self::$domain ) : $imap_errors[0] . '<br>' . __( 'You may want to disable certification validation temporarily.' );
				
				return new \WP_Error( 1, $error_message );
			}
			if ( ! in_array( $path, $list, true ) ) {
				$error_message = false !== $imapStream ? __( 'Failed to connect to host (connection timeout). Please review your settings and run test again.', self::$domain ) : __( 'Failed to find folder "sent items" (INBOX.Sent). Replies can not be saved on your mail server.', self::$domain );
				
				return new \WP_Error( 1, $error_message );
			}
		} catch( Exception $e ) {
			
			return new \WP_Error( 2, __( 'Internal Error: Unable to search for Folder.', self::$domain ) );
		}
		
		return true;
	}
	
	/**
	 * Checks if the md5 hash from the stored options is equal to the
	 * one of the options that will be in use.
	 *
	 * @param string $protocoll
	 *
	 * @since 0.9.0
	 * @return bool
	 */
	public static function settings_md5_match( $protocoll = 'smtp' ) {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( self::$domain );
		$stored_account_settings = $Bonaire_Options->get_stored_options( 0 );
		$stored_plugin_options = $Bonaire_Options->get_stored_options( 1 );
		$stored_smtp_hash = $stored_plugin_options->{$protocoll . '_hash'};
		
		if ( 'imap' === $protocoll ) {
			
			$array = array(
				'username' => $stored_account_settings->username,
				'password' => $stored_account_settings->password,
				'smtp_host' => $stored_account_settings->smtp_host,
				'smtp_port' => $stored_account_settings->smtp_port,
				'smtpsecure' => $stored_account_settings->smtpsecure,
				'fromname' => $stored_account_settings->fromname,
				'from' => $stored_account_settings->from,
				'imapsecure' => $stored_account_settings->imapsecure,
				'imap_host' => $stored_account_settings->imap_host,
				'imap_port' => $stored_account_settings->imap_port,
			);
		} else {
			
			$array = array(
				'username' => $stored_account_settings->username,
				'password' => $stored_account_settings->password,
				'smtp_host' => $stored_account_settings->smtp_host,
				'smtp_port' => $stored_account_settings->smtp_port,
				'smtpsecure' => $stored_account_settings->smtpsecure,
				'fromname' => $stored_account_settings->fromname,
				'from' => $stored_account_settings->from
			);
		}
		$hash = md5( serialize( $array ) );
		
		return $hash === $stored_smtp_hash;
	}
	
	/**
	 * Returns the result of the md5 check.
	 *
	 * @param string $protocoll
	 *
	 * @since 0.9.0
	 * @return bool
	 */
	public static function get_settings_md5_match( $protocoll ) {
		
		return self::settings_md5_match( $protocoll );
	}
	
}
