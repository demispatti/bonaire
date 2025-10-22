<?php
/**
 * Options Service
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Services;

/**
 * Handles plugin options management.
 *
 * @since 1.0.0
 */
class OptionsService {

	/**
	 * Text domain.
	 *
	 * @var string
	 */
	private $domain;

	/**
	 * Option key.
	 *
	 * @var string
	 */
	private $option_key = 'bonaire_options';

	/**
	 * Constructor.
	 *
	 * @param string $domain Text domain.
	 */
	public function __construct( string $domain ) {
		$this->domain = $domain;
	}

	/**
	 * Get stored options.
	 *
	 * @param int $group Option group (0=settings, 1=status).
	 * @return object|array
	 */
	public function get( int $group = 0 ) {
		$options = get_option( $this->option_key );

		if ( false === $options ) {
			return $this->get_defaults( $group );
		}

		return isset( $options[ $group ] ) ? (object) $options[ $group ] : $this->get_defaults( $group );
	}

	/**
	 * Save options.
	 *
	 * @param array $data   Options data to save.
	 * @param int   $group  Option group.
	 * @return bool
	 */
	public function save( array $data, int $group = 0 ): bool {
		$options = get_option( $this->option_key, [] );
		$options[ $group ] = $data;

		return update_option( $this->option_key, $options, true );
	}

	/**
	 * Get default options.
	 *
	 * @param int $group Option group.
	 * @return object
	 */
	public function get_defaults( int $group = 0 ): object {
		$defaults = [
			0 => [
				'channel'                       => '',
				'username'                      => '',
				'password'                      => '',
				'smtp_host'                     => '',
				'smtp_port'                     => 465,
				'smtpsecure'                    => 'ssl',
				'fromname'                      => '',
				'from'                          => '',
				'save_reply'                    => 'no',
				'imapsecure'                    => 'ssl',
				'imap_host'                     => '',
				'imap_port'                     => 993,
				'inbox_folder_name'             => 'Sent',
				'inbox_folder_path'             => '',
				'ssl_certification_validation'  => 'cert',
			],
			1 => [
				'smtp_status' => 'orange',
				'imap_status' => 'inactive',
			],
		];

		return (object) ( $defaults[ $group ] ?? [] );
	}

	/**
	 * Encrypt a string (for passwords).
	 *
	 * @param string $string String to encrypt.
	 * @return string
	 */
	public function encrypt( string $string ): string {
		$secret_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret_iv = defined( 'AUTH_SALT' ) ? AUTH_SALT : '';

		if ( empty( $secret_key ) || empty( $secret_iv ) ) {
			return $string;
		}

		$encrypt_method = 'AES-256-CBC';
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	}

	/**
	 * Decrypt a string (for passwords).
	 *
	 * @param string $string String to decrypt.
	 * @return string
	 */
	public function decrypt( string $string ): string {
		$secret_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret_iv = defined( 'AUTH_SALT' ) ? AUTH_SALT : '';

		if ( empty( $secret_key ) || empty( $secret_iv ) ) {
			return $string;
		}

		$encrypt_method = 'AES-256-CBC';
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		return openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}

	/**
	 * Validate options.
	 *
	 * @param array $input Raw input data.
	 * @return array Validated data.
	 */
	public function validate( array $input ): array {
		$output = [];

		foreach ( $input as $key => $value ) {
			$value = strip_tags( stripslashes( $value ) );

			switch ( $key ) {
				case 'smtp_host':
				case 'imap_host':
					$output[ $key ] = filter_var( $value, FILTER_VALIDATE_DOMAIN ) ? $value : '';
					break;

				case 'smtp_port':
				case 'imap_port':
					$output[ $key ] = filter_var( $value, FILTER_VALIDATE_INT ) ? (int) $value : '';
					break;

				case 'username':
				case 'from':
					$output[ $key ] = filter_var( $value, FILTER_VALIDATE_EMAIL ) ? $value : '';
					break;

				case 'password':
					$output[ $key ] = $value;
					break;

				case 'smtpsecure':
				case 'imapsecure':
					$output[ $key ] = in_array( $value, [ 'ssl', 'tls' ], true ) ? $value : 'ssl';
					break;

				case 'save_reply':
					$output[ $key ] = in_array( $value, [ 'yes', 'no' ], true ) ? $value : 'no';
					break;

				case 'ssl_certification_validation':
					$output[ $key ] = in_array( $value, [ 'cert', 'nocert' ], true ) ? $value : 'cert';
					break;

				default:
					$output[ $key ] = sanitize_text_field( $value );
			}
		}

		return $output;
	}
}
