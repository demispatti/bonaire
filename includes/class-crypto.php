<?php
/**
 * Credential encryption with authenticated ciphertext and legacy migration support.
 *
 * @package Bonaire
 */

namespace Bonaire\Includes;

if ( ! defined( 'WPINC' ) ) {
	die;
}

final class Bonaire_Crypto {
	private const PREFIX = 'bonaire:v2:';

	public static function encrypt( string $plaintext ): string {
		if ( '' === $plaintext ) {
			return '';
		}

		if ( ! function_exists( 'openssl_encrypt' ) ) {
			throw new \RuntimeException( 'Bonaire requires the OpenSSL PHP extension.' );
		}

		$iv         = random_bytes( 12 );
		$tag        = '';
		$ciphertext = openssl_encrypt(
			$plaintext,
			'aes-256-gcm',
			self::key(),
			OPENSSL_RAW_DATA,
			$iv,
			$tag,
			'bonaire',
			16
		);

		if ( false === $ciphertext ) {
			throw new \RuntimeException( 'Bonaire could not encrypt the credential.' );
		}

		return self::PREFIX . base64_encode( $iv . $tag . $ciphertext );
	}

	public static function decrypt( string $ciphertext ): string {
		if ( '' === $ciphertext ) {
			return '';
		}
		if ( ! function_exists( 'openssl_decrypt' ) ) {
			return '';
		}

		if ( str_starts_with( $ciphertext, self::PREFIX ) ) {
			$payload = base64_decode( substr( $ciphertext, strlen( self::PREFIX ) ), true );
			if ( false === $payload || strlen( $payload ) < 29 ) {
				return '';
			}

			$plaintext = openssl_decrypt(
				substr( $payload, 28 ),
				'aes-256-gcm',
				self::key(),
				OPENSSL_RAW_DATA,
				substr( $payload, 0, 12 ),
				substr( $payload, 12, 16 ),
				'bonaire'
			);

			return false === $plaintext ? '' : $plaintext;
		}

		// Only attempt migration with installation-specific WordPress salts.
		// Older releases could fall back to a shared key. That key is deliberately
		// not bundled: credentials written with it must be entered again.
		$auth_key  = defined( 'AUTH_KEY' ) ? (string) AUTH_KEY : '';
		$auth_salt = defined( 'AUTH_SALT' ) ? (string) AUTH_SALT : '';
		$legacy    = self::decrypt_legacy( $ciphertext, $auth_key, $auth_salt );

		return false === $legacy ? '' : $legacy;
	}

	public static function needs_migration( string $ciphertext ): bool {
		return '' !== $ciphertext && ! str_starts_with( $ciphertext, self::PREFIX );
	}

	private static function key(): string {
		$key  = defined( 'AUTH_KEY' ) ? (string) AUTH_KEY : wp_salt( 'auth' );
		$salt = defined( 'SECURE_AUTH_SALT' ) ? (string) SECURE_AUTH_SALT : wp_salt( 'secure_auth' );

		return hash( 'sha256', $key . $salt, true );
	}

	private static function decrypt_legacy( string $ciphertext, string $secret_key, string $secret_iv ) {
		if ( '' === $secret_key || '' === $secret_iv || ! function_exists( 'openssl_decrypt' ) ) {
			return false;
		}

		$encoded = base64_decode( $ciphertext, true );
		if ( false === $encoded ) {
			return false;
		}

		return openssl_decrypt(
			$encoded,
			'AES-256-CBC',
			hash( 'sha256', $secret_key ),
			0,
			substr( hash( 'sha256', $secret_iv ), 0, 16 )
		);
	}
}
