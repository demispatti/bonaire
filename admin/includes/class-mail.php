<?php
namespace Bonaire\Admin\Includes;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Bonaire\Admin\Includes as AdminIncludes;
use WP_Error;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! trait_exists( 'WPCF7_SWV_SchemaHolder' ) ) {
	include BONAIRE_PLUGINS_ROOT_DIR . 'contact-form-7/includes/swv/schema-holder.php';
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'PHPMailer\PHPMailer\PHPMailer' ) ) {
	include ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php';
}

/**
 * The class responsible for email functionality.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
final class Bonaire_Mail extends PHPMailer {
	
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
	 * Holds the stored options.
	 *
	 * @var object $stored_options
	 * @since    0.9.6
	 * @access   private
	 */
	private $stored_options;
	
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
		
		$secret_key = defined( AUTH_KEY ) ? AUTH_KEY : 'r4RWH*ynn!AS.|A-j<qph!#))@!Gde5i,0&Z[R=i.]78f[Ine)aChIMwRpqZN$6~';
		$secret_iv  = defined( AUTH_SALT ) ? AUTH_SALT : '=;.6h~xr5v/BZuKP-|GR B*Kb`K-Q@PH6r>My6=-gz$qTt+X!0Rc_6>N:&g5&1>R';
		
		if ( '' === $secret_key || '' === $secret_iv ) {
			return $string;
		}
		
		$encrypt_method = 'AES-256-CBC';
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		
		return openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
	
	/**
	 * Bonaire_Mail constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 */
	public function __construct( $domain, $Bonaire_Options ) {
		
		parent::__construct();
		
		$this->domain          = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->stored_options  = $Bonaire_Options->get_stored_options();
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
	private function setup( $data, $exceptions = null ) {
		
		$mail = $this->phpmailer( $exceptions );
		$mail->AddAddress( $data->to );
		$mail->AddReplyTo( $this->stored_options->from );
		$mail->Subject  = strip_tags( $data->subject );
		$mail->Body     = strip_tags( $data->message );
		$mail->From     = $this->stored_options->from;
		$mail->FromName = $data->fromname;
		$mail->isSMTP();
		
		return $mail;
	}
	
	/**
	 * Sends mail trough PHPMailer.
	 *
	 * @param object $data
	 *
	 * @return bool|\WP_Error
	 * @throws \Exception If saving the message failed
	 * @since 0.9.6
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
			
			return new WP_Error( - 2, __( 'Sending message failed.', $this->domain ) );
		}
		
		// Maybe save message in "Sent" folder
		if ( 'yes' === $this->stored_options->save_reply ) {
			
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
	 * @return bool|\WP_Error
	 * @since 0.9.6
	 */
	private function save_message( $mail ) {
		
		try {
			
			$ssl_certification_validation = 'nocert' === $this->stored_options->ssl_certification_validation ? '/novalidate-cert' : '';
			$mailbox                      = $this->get_mailbox( $mail, $ssl_certification_validation );
			$sent_items_folder            = $this->get_sent_items_folder_for_send_mail( $mail );
			
			$message = $mail->MIMEHeader . $mail->MIMEBody;
			$imapStream = imap_open( $mailbox, $mail->Username, $mail->Password ) or die( 'Cannot connect to web server: ' . imap_last_error() );
			$result = imap_append( $imapStream, $sent_items_folder, $message );
			imap_close( $imapStream );
			
			if ( false === $imapStream ) {
				
				return new WP_Error( 0, __( 'Failed to connect to host. Please review your settings and try again.', $this->domain ) );
			}
			if ( false === $result ) {
				
				return new WP_Error( 0, __( 'Message sent, but failed to save it in your mail server\'s default folder for sent items. Please review your settings and try again.', $this->domain ) );
			}
			
			return true;
		} catch( Exception $ex ) {
			
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
	 * @param $ssl_certification_validation
	 *
	 * @return string
	 */
	private function get_mailbox( $mail, $ssl_certification_validation ) {
		
		$mail->Host       = $this->stored_options->imap_host;
		$mail->Port       = $this->stored_options->imap_port;
		$mail->SMTPSecure = $this->stored_options->imapsecure;
		
		$mailserver_path = '{' . $mail->Host . ':' . $mail->Port . '/imap/' . $mail->SMTPSecure . '/' . $ssl_certification_validation . '}';
		$mailbox         = $mailserver_path . 'INBOX';
		
		return $mailbox;
	}
	
	/**
	 * Returns the path to the user's Sent Items folder.
	 *
	 * @param $mail
	 *
	 * @return string
	 */
	private function get_sent_items_folder_for_send_mail( $mail ) {
		
		$is_gmail          = $this->is_gmail();
		$inbox             = $is_gmail ? "[Gmail]/" : "INBOX";
		$inbox_folder_name = $is_gmail && '' !== $this->stored_options->inbox_folder_name ? $this->stored_options->inbox_folder_name : '.Sent';
		
		return '{' . $mail->Host . '}' . $inbox . $inbox_folder_name;
	}
	
	private function is_gmail() {
		
		return "smtp.gmail.com" === $this->stored_options->smtp_host;
	}
	
}
