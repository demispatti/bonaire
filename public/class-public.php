<?php

namespace Bonaire\Pub;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The public-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @since     0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/public
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Public {
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function add_hooks() {
		
		add_filter( 'wpcf7_posted_data', array( $this, 'filter_wpcf7_posted_data' ), 10, 1 );
		add_action( 'wpcf7_mail_sent', array( $this, 'wpcf7_after_mail_sent' ), 10 );
	}
	
	/**
	 * Creates or extends a transient containing
	 * the internal 'contact form id' and a 'uniqid' attached as
	 * field for identification purposes. The uniqid will be removed
	 * from the posted data after having been processed on the admin side.
	 *
	 * @see /admin/includes/class-adapter.php / postprocess_messages()
	 *
	 * @param $posted_data
	 *
	 * @since 0.9.6
	 * @return array $posted_data
	 */
	public function filter_wpcf7_posted_data( $posted_data ) {
		
		$uniqid = uniqid();
		
		$current_mails = get_transient( 'bonaire_wpcf7_incoming' );
		$data = array( 'form_id' => $posted_data['_wpcf7'], 'posted_data_uniqid' => $uniqid );
		$current_mails[] = $data;
		// Store the data temporarily for usage by 'wpcf7_after_mail_sent()'
		set_transient( 'bonaire_wpcf7_incoming', $current_mails );
		
		$posted_data['posted_data_uniqid'] = $uniqid;
		
		return $posted_data;
	}
	
	/**
	 * Retrieves the 'contact form name (channel)' and the 'recipient email address' in order to
	 * be able to postprocess the message and relate it to the currently used email account.
	 *
	 * @param \WPCF7_ContactForm $contact_form
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function wpcf7_after_mail_sent( $contact_form ) {
		
		$current_mails = get_transient( 'bonaire_wpcf7_incoming' );
		if(false === $current_mails || ! is_array($current_mails)){
			return;
		}
		
		foreach ( $current_mails as $i => $current_mail ) {
			
			if ( ! isset( $current_mail['recipient'] ) ) {
				$current_mails[ $i ]['channel'] = $contact_form->name();
				$current_mails[ $i ]['form_id'] = $contact_form->id();
				$properties = $contact_form->get_properties();
				$current_mails[ $i ]['recipient'] = $this->crypt(sanitize_email( $properties['mail']['recipient'] ));
			}
		}
		
		// Add the updated data to a transient for postprocessing by class '$Bonaire_Adapter'
		set_transient( 'bonaire_wpcf7_queue', $current_mails );
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
		
		$secret_key = AUTH_KEY;
		$secret_iv  = AUTH_SALT;
		
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
	
}
