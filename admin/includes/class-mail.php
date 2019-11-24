<?php

namespace Bonaire\Admin\Includes;

use Exception;
use PHPMailer;
use Bonaire\Admin\Includes as AdminIncludes;
use WP_Error;

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
 * The class responsible for email functionality.
 *
 * @since            0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
final class Bonaire_Mail extends PHPMailer {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since   0.9.6
	 * @access   protected
	 */
	protected $domain;
	
	/**
	 * Holds the instance of the class responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since   0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Holds the stored options.
	 *
	 * @var object $stored_options
	 * @since   0.9.6
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Sets up an instance of the mailer class.
	 *
	 * @param null $exceptions
	 *
	 * @since 0.9.6
	 * @return PHPMailer $mail
	 */
	private function phpmailer( $exceptions = null ) {
		// Create Instance
		$mail = new parent;
		// Setup
		$mail->Host = $this->stored_options->smtp_host;
		$mail->CharSet = 'utf-8';
		$mail->SMTPAuth = true;
		$mail->Port = $this->stored_options->smtp_port;
		$mail->From = $this->stored_options->from;
		$mail->FromName = $this->stored_options->fromname;
		$mail->Username = $this->stored_options->username;
		$mail->Password = $this->decrypt( $this->stored_options->password );
		$mail->SMTPSecure = $this->stored_options->smtpsecure;
		$mail->isSMTP();
		
		// Debug
		if ( null !== $exceptions ) {
			$mail->SMTPDebug = 2;
			$mail->Debugoutput = function ( $str, $level ) {
				
				global $debug;
				$debug[] .= "$level: $str\n";
			};
			$mail->Timeout = 5;
		}
		
		return $mail;
	}
	
	/**
	 * Decrypts the password for the email account stored for replies.
	 *
	 * @param string $string
	 *
	 * @since 0.9.6
	 * @return string $output|bool
	 * @see \Bonaire\Admin\Includes\Bonaire_Options crypt()
	 */
	private function decrypt( $string ) {
		
		$secret_key = AUTH_KEY;
		$secret_iv = AUTH_SALT;
		
		if ( '' === $secret_key || '' === $secret_iv ) {
			return $string;
		}
		
		$encrypt_method = 'AES-256-CBC';
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		
		return $output;
	}
	
	/**
	 * Bonaire_Mail constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 */
	public function __construct( $domain, $Bonaire_Options ) {
		
		parent::__construct();
		
		$this->domain = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->stored_options = $Bonaire_Options->get_stored_options();
	}
	
	/**
	 * Sets up the mailer instance.
	 *
	 * @param object $data
	 * @param null $exceptions
	 *
	 * @since 0.9.6
	 * @return PHPMailer $mail
	 */
	private function setup( $data, $exceptions = null ) {
		
		$to = null !== $data->to ? (string) $data->to : $this->stored_options->from;
		
		$mail = $this->phpmailer( $exceptions );
		$mail->AddAddress( $to );
		$mail->AddReplyTo( $this->stored_options->from );
		$mail->Subject = strip_tags( $data->subject );
		$mail->Body = strip_tags( $data->message );
		$mail->From = $this->stored_options->from;
		$mail->FromName = $data->fromname;
		$mail->isSMTP();
		
		return $mail;
	}

	/**
	 * Sends mail trough PHPMailer.
	 *
	 * @param object $data
	 *
	 * @since 0.9.6
	 * @return bool|\WP_Error
	 * @throws \Exception If saving the message failed
	 */
	public function send_mail( $data ) {
		
		$mail = $this->setup( $data );
		
		try {
			$result = $mail->Send();
		} catch( Exception $e ) {
			
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
		
		// If sending the message failed
		if ( false === $result ) {
			
			return new WP_Error( - 2, __( 'Sending test message failed:', $this->domain ) . ' ' . __( 'Could not reach the mail server.', $this->domain ) . '<br>' . __( 'Please make sure that you are connected to the internet, and that you\'ve tested the SMTP and IMAP settings with the respective buttons on this plugin\'s settings page.', $this->domain ) );
		}
		
		// Maybe save message in "Sent" folder
		if ( $result && 'yes' === $this->stored_options->save_reply ) {
			
			try {
				$result = $this->save_message( $mail );
			} catch( Exception $e ) {
				
				return new WP_Error( $e->getCode(), $e->getMessage() );
			}
		}
		
		return $result;
	}
	
	/**
	 * Saves the message in the INBOX folder for sent items.
	 *
	 * @param PHPMailer $mail
	 *
	 * @since 0.9.6
	 * @return bool|\WP_Error
	 */
	private function save_message( $mail ) {
		
		try {

			$novalidate = 'nocert' === $this->stored_options->ssl_certification_validation ? 'novalidate-cert' : '';
			$mailbox = $this->get_mailbox( $mail, $novalidate );
			$sent_items_folder = $this->get_sent_items_folder_for_send_mail( $mailbox );
			
			$message = $mail->MIMEHeader . $mail->MIMEBody;
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or die( 'Cannot connect to web server: ' . imap_last_error() );
			imap_append( $imapStream, $sent_items_folder, $message );
			imap_close( $imapStream );
			
			if ( false === $imapStream ) {
				
				return new WP_Error( 0, __( 'Failed to connect to host. Please review your settings and try again.', $this->domain ) );
			}
			
			return true;
			
		} catch( Exception $ex) {
			
			return new WP_Error( 0, __( 'Failed to connect to host. Please review your settings and try again.', $this->domain ) );
		}
	}

	/**
	 * Checks if SSL is enabled.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_ssl() {
		
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
		
		return false;
	}

	/**
	 * Returns the path to the mailbox on the mail server.
	 *
	 * @param $mail
	 * @param $novalidate
	 *
	 * @return string
	 */
	private function get_mailbox($mail, $novalidate) {
		
		$mail->Host = $this->stored_options->imap_host;
		$mail->Port = $this->stored_options->imap_port;
		$mail->SMTPSecure = $this->stored_options->imapsecure;

		$mailserver_path = '{' . $mail->Host . ':' . $mail->Port . '/imap/' . $mail->SMTPSecure . '/' . $novalidate . '}';
		$mailbox = $mailserver_path . 'INBOX';
		
		return $mailbox;
	}
	
	/**
	 * Returns the path to the user's Sent Items folder.
	 *
	 * @param $mail
	 *
	 * @return string
	 */
	private function get_sent_items_folder_for_send_mail($mail) {
		
		$inbox = "imap.gmail.com" === $this->stored_options->imap_host ? "[Gmail]/" : "INBOX.";
		
		$inbox_folder_name = '' !== $this->stored_options->inbox_folder_name ? $this->stored_options->inbox_folder_name : 'Sent';
		
		return '{' . $mail->Host . '}' . $inbox . $inbox_folder_name;
	}

}
