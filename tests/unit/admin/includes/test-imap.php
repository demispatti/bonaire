<?php

if ( ! class_exists( 'PHPMailer' ) ) {
	include ABSPATH . WPINC . '/class-phpmailer.php';
}

if ( ! class_exists( 'Ping' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ping.php';
}

class Bonaire_Imap {
	
	private $domain;
	
	/**
	 * @var stdClass $stored_options
	 */
	private $stored_options;
	
	/**
	 * @var PHPMailer $PHPMailer
	 */
	private $PHPMailer;
	
	private function set_phpmailer() {
		
		$mail = new PHPMailer();
		$mail->Host = $this->stored_options->smtp_host;
		$mail->SMTPAuth = true;
		$mail->Port = $this->stored_options->smtp_port;
		$mail->Username = $this->stored_options->username;
		$mail->Password = $this->stored_options->password;
		$mail->SMTPSecure = $this->stored_options->smtpsecure;
		$mail->isSMTP();
		
		$this->PHPMailer = $mail;
	}
	
	public function __construct( $domain, $stored_options ) {
		
		$this->domain = $domain;
		$this->stored_options = $stored_options;
		
		$this->set_phpmailer();
	}
	
	/**
	 * @return bool|\WP_Error
	 */
	public function init() {
		
		return $this->imap_diagnose();
	}
	
	/**
	 * @return bool|array
	 */
	private function imap_diagnose() {
		
		$imap_host = $this->stored_options->smtp_host;
		$imap_port = $this->stored_options->smtp_port;
		$imap_ports = array( $this->stored_options->smtp_port );
		
		$error = null;
		$successes = array();
		
		//Resolve host name
		$resolve_result = $this->resolve_hostname( $imap_host );
		if ( is_wp_error( $resolve_result ) ) {
			
			$error['resolve_smtp_hostname'] = $resolve_result->get_error_message();
			
			return array( 'successes' => $successes, 'error' => $error );
		}
		$successes['resolve_smtp_hostname'] = __( 'Successfully resolved host name.', $this->domain );
		
		// Ping host
		$ping_result = $this->ping_host( $imap_host );
		if ( is_wp_error( $ping_result ) ) {
			
			$error['ping_smtp_host'] = $ping_result->get_error_message();
			
			return array( 'successes' => $successes, 'error' => $error );
		}
		$successes['ping_smtp_host'] = __( 'Successfully pinged host.', $this->domain );
		
		// Test SMTP port
		$port_result = $this->test_imap_port( $imap_host, $imap_ports );
		if ( is_wp_error( $port_result ) ) {
			
			$error['imap_port'] = $port_result->get_error_message();
			
			return array( 'successes' => $successes, 'error' => $error );
		}
		$successes['imap_port'] = __( 'Successfully tested SMTP port.', $this->domain );
		
		// Test user credentials
		$socket = fsockopen( $imap_host, $imap_port, $errno, $errstr, 2 );
		$credentials_result = $this->test_credentials();
		fclose( $socket );
		if ( is_wp_error( $credentials_result ) ) {
			
			$error['test_credentials'] = $credentials_result->get_error_message();
			
			return array( 'successes' => $successes, 'error' => $error );
		}
		$successes['test_credentials'] = __( 'Successfully authenticated to the account.', $this->domain );
		
		return array( 'successes' => $successes, 'error' => false );
	}
	
	private function resolve_hostname( $imap_host ) {
		
		try {
			$test_dns = gethostbyname( $imap_host . '.' );
			
			if ( preg_match( '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $test_dns ) ) {
				
				return true;
			}
			
			return new WP_Error( 1, __( 'Failed to resolve SMTP hostname (' . $imap_host . ') - you appear to have a DNS resolution issue with your hosting account.', $this->domain ) );
		} catch( Exception $e ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to resolve host name.', $this->domain ) );
		}
	}
	
	private function ping_host( $imap_host ) {
		
		try {
			$ping = new Ping( $imap_host );
			$latency = $ping->ping();
			if ( $latency !== false ) {
				
				return true;
			}
			
			return new WP_Error( 3, __( 'Failed to ping host (' . $imap_host . '). Please review the settings for your SMTP host.', $this->domain ) );
		} catch( Exception $e ) {
			
			return new WP_Error( 4, __( 'Internal Error: Unable to ping host.', $this->domain ) );
		}
	}
	
	private function test_imap_port( $imap_host, $imap_ports ) {
		
		try {
			$result = null;
			
			if ( $socket = fsockopen( $imap_host, $imap_ports[0], $errno, $errstr, 2 ) ) {
				fclose( $socket );
				flush();
				$result = true;
			} else {
				flush();
				$result = new WP_Error( 5, __( 'The port number is wrong. Please review the port number of your SMTP server. (' . $errstr . ')', $this->domain ) );
			}
			unset( $socket );
			
			return $result;
		} catch( Exception $error ) {
			
			return new WP_Error( 6, __( 'Internal Error: Unable to check port.', $this->domain ) );
		}
	}
	
	private function test_credentials() {
		
		try {
			$result = $this->PHPMailer->SmtpConnect();
			if ( false === $result ) {
				
				return new WP_Error( 7, __( 'Failed to authenticate. Please review your username and password for the email account you want to use.', $this->domain ) );
			}
		} catch( Exception $error ) {
			
			return new WP_Error( 8, __( 'Internal Error: Unable to check SMTP authentication (username / password).', $this->domain ) );
		}
		
		return true;
	}
	
	private function get_inbox_sent_folder() {
		
		$host = '{imap.gmail.com:993/ssl}';
		$mail_con = imap_open( $host, $login, $password );
		$mailboxes = imap_list( $mail_con, $host, '*' );
		
		$host = '{imap.gmail.com:993/ssl}INBOX.Sent';
		$mail_con = imap_open( $host, $login, $password );
	}
	
}
