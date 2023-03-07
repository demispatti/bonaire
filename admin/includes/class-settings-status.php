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
		
		if ( false !== $protocol ) {
			
			if ( $bool ) {
				
				return isset( $stored_options[1][ $protocol . '_status' ]) && 'green' === $stored_options[1][ $protocol . '_status' ];
			} else {
				
				return isset( $stored_options[1][ $protocol . '_status' ] ) ? $stored_options[1][ $protocol . '_status' ] : 'orange';
			}
		} else {
			
			return array(
				'cf7' => isset( $stored_options[1]['cf7_status'] ) ? $stored_options[1]['cf7_status'] : 'orange',
				'smtp' => isset( $stored_options[1]['smtp_status'] ) ? $stored_options[1]['smtp_status'] : 'orange',
				'imap' => isset( $stored_options[1]['imap_status'] ) ? $stored_options[1]['imap_status'] : 'orange'
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
		
		$stored_options                             = get_option( 'bonaire_options' );
		$stored_options[1][ $protocol . '_status' ] = $status;
		
		try {
			update_option( 'bonaire_options', $stored_options, true );
		} catch( Exception $e ) {
			
			return new WP_Error( 1, __( 'Internal Error: Unable to set settings status.', $this->domain ) );
		}
		
		return true;
	}
	
}
