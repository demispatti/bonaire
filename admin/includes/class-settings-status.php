<?php
namespace Bonaire\Admin\Includes;

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
 * @since             1.0.0
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
final class Bonaire_Settings_Status {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Bonaire_Options constructor.
	 *
	 * @param $domain
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
	}
	
	/**
	 * Returns true if positively evaluated email account settings were found,
	 * otherwise it returns false.
	 *
	 * @param string|bool $protocol
	 * @param bool $bool
	 *
	 * @return bool|array
	 * @since 0.9.6
	 */
	public function get_settings_status( $protocol = false, $bool = false ) {
		
		$stored_options = get_option( 'bonaire_options' );
		if ( is_object( $stored_options ) ) {
			$stored_options = get_object_vars( $stored_options );
		}
		if ( ! is_array( $stored_options ) ) {
			$stored_options = array();
		}
		$status_options = $stored_options[1] ?? array();
		if ( is_object( $status_options ) ) {
			$status_options = get_object_vars( $status_options );
		}
		if ( ! is_array( $status_options ) ) {
			$status_options = array();
		}
		
		if ( false !== $protocol ) {
			
			if ( $bool ) {
				
				return isset( $status_options[ $protocol . '_status' ] ) && 'green' === $status_options[ $protocol . '_status' ];
			} else {
				
				return isset( $status_options[ $protocol . '_status' ] ) ? $status_options[ $protocol . '_status' ] : 'orange';
			}
		} else {
			
			return array(
				'cf7' => isset( $status_options['cf7_status'] ) ? $status_options['cf7_status'] : 'orange',
				'smtp' => isset( $status_options['smtp_status'] ) ? $status_options['smtp_status'] : 'orange',
				'imap' => isset( $status_options['imap_status'] ) ? $status_options['imap_status'] : 'orange'
			);
		}
	}
	
	/**
	 * Sets the status of the evaluated email account settings.
	 * The settings are:
	 * - green, the settings were successfully evaluated. Replies to messages can be sent / stored in 'INBOX.Sent' (if IMAP is configuerd)
	 * - orange, the email account settings are complete but evaluation failed
	 * - red, the email account settings are incomplete (empty input fields left on the settings page)
	 *
	 * @param string $protocol
	 * @param string $status
	 *
	 * @return bool|\WP_Error
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function set_settings_status( $protocol, $status ) {
		
		$stored_options = get_option( 'bonaire_options' );
		if ( is_object( $stored_options ) ) {
			$stored_options = get_object_vars( $stored_options );
		}
		if ( ! is_array( $stored_options ) ) {
			$stored_options = array( array(), array() );
		}
		if ( ! isset( $stored_options[1] ) || ! is_array( $stored_options[1] ) ) {
			$stored_options[1] = is_object( $stored_options[1] ?? null )
				? get_object_vars( $stored_options[1] )
				: array();
		}
		$stored_options[1][ $protocol . '_status' ] = $status;
		
		try {
			update_option( 'bonaire_options', $stored_options, false );
		} catch( \Throwable $e ) {
			
			return new WP_Error( 1, __( 'Internal Error: Unable to set settings status.', "bonaire" ) );
		}
		
		return true;
	}
	
}
