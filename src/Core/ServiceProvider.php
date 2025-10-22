<?php
/**
 * Abstract Service Provider
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Core;

use Bonaire\Core\Contracts\ServiceProviderInterface;

/**
 * Base service provider class.
 *
 * @since 1.0.0
 */
abstract class ServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		// Default implementation - override in child classes.
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( Container $container ): void {
		// Default implementation - override in child classes.
	}
}
