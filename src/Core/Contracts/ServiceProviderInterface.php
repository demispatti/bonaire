<?php
/**
 * Service Provider Interface
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Core\Contracts;

use Bonaire\Core\Container;

/**
 * Interface for service providers.
 *
 * @since 1.0.0
 */
interface ServiceProviderInterface {

	/**
	 * Register services with the container.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function register( Container $container ): void;

	/**
	 * Bootstrap services after all providers are registered.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	public function boot( Container $container ): void;
}
