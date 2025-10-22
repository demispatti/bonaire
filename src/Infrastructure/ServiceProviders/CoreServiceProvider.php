<?php
/**
 * Core Service Provider
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Infrastructure\ServiceProviders;

use Bonaire\Core\ServiceProvider;
use Bonaire\Core\Container;
use Bonaire\Infrastructure\Services\I18nService;
use Bonaire\Infrastructure\Services\ActivationService;
use Bonaire\Infrastructure\Services\DeactivationService;

/**
 * Service provider for core functionality.
 *
 * @since 1.0.0
 */
class CoreServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$this->register_i18n( $container );
		$this->register_lifecycle_services( $container );
	}

	/**
	 * Register internationalization service.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	private function register_i18n( Container $container ): void {
		$container->singleton(
			I18nService::class,
			function ( Container $c ) {
				return new I18nService( $c->get( 'plugin.domain' ) );
			}
		);
	}

	/**
	 * Register lifecycle services (activation/deactivation).
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	private function register_lifecycle_services( Container $container ): void {
		$container->singleton( ActivationService::class, fn() => new ActivationService() );
		$container->singleton( DeactivationService::class, fn() => new DeactivationService() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( Container $container ): void {
		// Load text domain.
		$container->get( I18nService::class )->load_text_domain();
	}
}
