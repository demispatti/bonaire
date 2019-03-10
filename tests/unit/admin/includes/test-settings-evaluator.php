<?php

if ( ! class_exists( 'Ping' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ping.php';
}

class Bonaire_Settings_Evaluator {
	
	/**
	 * @var string $domain
	 */
	private $domain;
	
	private $stored_options;
	
	private $options_meta;
	
	/**
	 * @var \Bonaire_Options $Bonaire_Options
	 */
	private $Bonaire_Options;
	
	/**
	 * @var PHPMailer $PHPMailer
	 */
	private $PHPMailer;
	
	/**
	 * Bonaire_Settings_Evaluator constructor.
	 *
	 * @param string $domain
	 * @param Bonaire_Options $Bonaire_Options
	 * @param Bonaire_Mail $Bonaire_Mail
	 */
	public function __construct( $domain, $Bonaire_Options, $Bonaire_Mail ) {
		
		$this->domain = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->stored_options = $Bonaire_Options->get_stored_options( '0' );
		$this->options_meta = $Bonaire_Options->get_options_meta();
		$this->PHPMailer = $Bonaire_Mail->get_phpmailer( true );
	}
	
	/**
	 * @return bool|string|\WP_Error
	 */
	public function bonaire_test_smtp_settings() {
		
		return $this->test_smtp_settings();
	}
	
	/**
	 * Check if the form is filled out completely. If so, evaluate the SMTP settings.
	 *
	 * @param bool $internal
	 *
	 * @return bool|string|\WP_Error
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
		
		return new WP_Error( 1, __( 'Please fill in all details first.', $this->domain ) );
	}
	
	/**
	 * @return bool|string|\WP_Error
	 */
	public function bonaire_test_imap_settings() {
		
		return $this->test_imap_settings();
	}
	
	/**
	 * Check if the form is filled out completely. If so, evaluate the IMAP settings.
	 *
	 * @param bool $internal
	 *
	 * @return bool|string|\WP_Error
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
		
		return new WP_Error( 1, __( 'Please fill in all details first.', $this->domain ) );
	}
	
	/**
	 * @param $protocol
	 *
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
	 * @return bool|array
	 */
	private function evaluate_smtp_settings() {
		
		$smtp_host = $this->stored_options->smtp_host;
		$smtp_port = $this->stored_options->smtp_port;
		$smtp_ports = array( $this->stored_options->smtp_port );
		
		return $this->run_smtp_evaluation( $smtp_host, $smtp_port, $smtp_ports );
	}
	
	/**
	 * @param $smtp_host
	 * @param $smtp_port
	 * @param $smtp_ports
	 *
	 * @return array
	 */
	private function run_smtp_evaluation( $smtp_host, $smtp_port, $smtp_ports ) {
		
		$response = null;
		$successes = null;
		
		//Resolve SMTP hostname
		$resolve_result = $this->resolve_smtp_hostname( $smtp_host );
		if ( is_wp_error( $resolve_result ) ) {
			
			return array( 'success' => $successes, 'error' => $resolve_result->get_error_message() );
		}
		$successes[0] = __( 'Successfully resolved host name.', $this->domain );
		
		// Ping SMTP host
		$ping_result = $this->ping_smtp_host( $smtp_host );
		if ( is_wp_error( $ping_result ) ) {
			
			return array( 'success' => $successes, 'error' => $ping_result->get_error_message() );
		}
		$successes[1] = __( 'Successfully pinged host.', $this->domain );
		
		// Test SMTP port
		$port_result = $this->test_smtp_port( $smtp_host, $smtp_ports );
		if ( is_wp_error( $port_result ) ) {
			
			return array( 'success' => $successes, 'error' => $port_result->get_error_message() );
		}
		$successes[2] = __( 'Successfully tested SMTP port.', $this->domain );
		
		// Test SMTP user credentials
		$socket = fsockopen( $smtp_host, $smtp_port, $errno, $errstr, 2 );
		$credentials_result = $this->test_credentials();
		fclose( $socket );
		if ( is_wp_error( $credentials_result ) ) {
			
			return array( 'success' => $successes, 'error' => $credentials_result->get_error_message() );
		}
		$successes[3] = __( 'Successfully authenticated to the account via SMTP.', $this->domain );
		
		// Add final success message.
		$successes[4] = __( 'Successfully evaluated SMTP settings!', $this->domain );
		
		return array( 'success' => $successes, 'error' => false );
	}
	
	/**
	 * @return array
	 */
	private function evaluate_imap_settings() {
		
		return $this->run_imap_evaluation();
	}
	
	/**
	 * @return array
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
		$successes[0] = __( 'Successfully resolved host name.', $this->domain );
		
		// Ping IMAP host
		$ping_result = $this->ping_imap_host( $imap_host );
		if ( is_wp_error( $ping_result ) ) {
			
			return array( 'success' => $successes, 'error' => $ping_result->get_error_message() );
		}
		$successes[1] = __( 'Successfully pinged host.', $this->domain );
		
		// Test IMAP port
		$port_result = $this->test_imap_port( $imap_host, $imap_ports );
		if ( is_wp_error( $port_result ) ) {
			
			return array( 'success' => $successes, 'error' => $port_result->get_error_message() );
		}
		$successes[2] = __( 'Successfully tested IMAP port.', $this->domain );
		
		// Test INBOX.Sent folder
		$folder_result = $this->test_inbox();
		if ( is_wp_error( $folder_result ) ) {
			
			return array( 'success' => $successes, 'error' => $folder_result->get_error_message() );
		}
		$successes[3] = __( 'Successfully contacted INBOX.Sent folder.', $this->domain );
		
		// Add final success message.
		$successes[4] = __( 'Successfully evaluated IMAP settings!', $this->domain );
		
		return array( 'success' => $successes, 'error' => false );
	}
	
	/**
	 * @param $result
	 *
	 * @return array|\WP_Error
	 */
	private function create_response( $result, $state ) {
		
		$string = '';
		if ( false !== $result['error'] ) {
			
			return new WP_Error( 1, $result['error'] );
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
	 * @param $smtp_host
	 *
	 * @return bool|\WP_Error
	 */
	private function resolve_smtp_hostname( $smtp_host ) {
		
		try {
			$test_dns = gethostbyname( $smtp_host . '.' );
			
			if ( ! preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				
				return new WP_Error( 1, __( 'Failed to resolve SMTP hostname (' . $smtp_host . ') - you appear to have a DNS resolution issue with your hosting account.', $this->domain ) );
			}
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to resolve SMTP hostname.', $this->domain ) );
		}
		
		return true;
	}
	
	/**
	 * @param $smtp_host
	 *
	 * @return bool|\WP_Error
	 */
	private function ping_smtp_host( $smtp_host ) {
		
		try {
			$ping = new Ping( $smtp_host );
			$latency = $ping->ping();
			if ( $latency === false ) {
				
				return new WP_Error( 1, __( 'Failed to ping host (' . $smtp_host . '). Please review the settings for your SMTP host.', $this->domain ) );
			}
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to ping SMTP host.', $this->domain ) );
		}
		
		return true;
	}
	
	/**
	 * @param $smtp_host
	 * @param $smtp_ports
	 *
	 * @return bool|null|\WP_Error
	 */
	private function test_smtp_port( $smtp_host, $smtp_ports ) {
		
		try {
			$result = null;
			
			if ( $socket = fsockopen( $smtp_host, $smtp_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$result = new WP_Error( 1, __( 'The port number is wrong. Please review the port number of your SMTP server. (' . $errstr . ')', $this->domain ) );
			}
			unset( $socket );
		} catch( Exception $error ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to check SMTP port.', $this->domain ) );
		}
		
		return $result;
	}
	
	/**
	 * @return bool|\WP_Error
	 */
	private function test_credentials() {
		
		try {
			$this->PHPMailer->SmtpConnect();
		} catch( Exception $error ) {
			
			return new WP_Error( 1, __( 'SMTP Error: Could not authenticate.', $this->domain ) . ' ' . __( 'Please review your username and password.', $this->domain ) );
		}
		
		return true;
	}
	
	/**
	 * @param $smtp_host
	 *
	 * @return bool|\WP_Error
	 */
	private function resolve_imap_hostname( $smtp_host ) {
		
		try {
			$test_dns = gethostbyname( $smtp_host . '.' );
			
			if ( ! preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				
				return new WP_Error( 1, __( 'Failed to resolve IMAP hostname (' . $smtp_host . ') - you appear to have a DNS resolution issue with your hosting account.', $this->domain ) );
			}
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to resolve IMAP hostname.', $this->domain ) );
		}
		
		return true;
	}
	
	/**
	 * @param $smtp_host
	 *
	 * @return bool|\WP_Error
	 */
	private function ping_imap_host( $smtp_host ) {
		
		try {
			$ping = new Ping( $smtp_host );
			$latency = $ping->ping();
			if ( $latency !== false ) {
				
				return true;
			}
			
			return new WP_Error( 1, __( 'Failed to ping host (' . $smtp_host . '). Please review the settings for your IMAP host.', $this->domain ) );
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to ping IMAP host.', $this->domain ) );
		}
	}
	
	/**
	 * @param $smtp_host
	 * @param $smtp_ports
	 *
	 * @return bool|null|\WP_Error
	 */
	private function test_imap_port( $smtp_host, $smtp_ports ) {
		
		try {
			$result = null;
			
			if ( $socket = fsockopen( $smtp_host, $smtp_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$result = new WP_Error( 1, __( 'Failed to evaluate port number. Please review the port number of your IMAP server. (' . $errstr . ')', $this->domain ) );
			}
			unset( $socket );
		} catch( Exception $error ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to check IMAP port.', $this->domain ) );
		}
		
		return $result;
	}
	
	/**
	 * @return bool|\WP_Error
	 */
	private function test_inbox() {
		
		$mail = $this->PHPMailer;
		
		try {
			$mail->Host = $this->stored_options->imap_host;
			$mail->Port = $this->stored_options->imap_port;
			$mail->SMTPSecure = $this->stored_options->imapsecure;
			$mailbox = '{' . $mail->Host . '/' . $mail->SMTPSecure . '}';
			$path = '{' . $this->stored_options->imap_host . '}' . 'INBOX.Sent';
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password );
			$list = imap_list( $imapStream, '{' . $this->stored_options->imap_host . '}', '*' );
			imap_close( $imapStream );
			
			if ( false === $imapStream || ! in_array( $path, $list, true ) ) {
				$error_message = false === $imapStream ? __( 'Failed to connect to host (connection timeout). Please review your settings and run test again.', $this->domain ) : __( 'Failed. Folder "INBOX.Sent" not found. Replies can not be saved on your mail server.', $this->domain );
				
				return new WP_Error( 1, $error_message );
			}
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to search for Folder.', $this->domain ) );
		}
		
		return true;
	}
	
	/**
	 * @param string $protocoll
	 *
	 * @return bool
	 */
	private function is_valid_account_settings( $protocoll = 'smtp' ) {
		
		$Bonaire_Options = new Bonaire_Options( $this->domain );
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
	 * @param $protocoll
	 *
	 * @return bool
	 */
	public function get_is_valid_account_settings( $protocoll ) {
		
		return $this->is_valid_account_settings( $protocoll );
	}
	
}
