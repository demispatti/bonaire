<?php
/**
 * Frontend Service Provider
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Infrastructure\ServiceProviders;

use Bonaire\Core\ServiceProvider;
use Bonaire\Core\Container;
use Bonaire\Frontend\Controllers\FrontendController;

/**
 * Service provider for frontend functionality.
 *
 * @since 1.0.0
 */
class FrontendServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		if ( is_admin() ) {
			return;
		}

		$container->singleton(
			FrontendController::class,
			fn( Container $c ) => $c->make( FrontendController::class )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( Container $container ): void {
		if ( is_admin() ) {
			return;
		}

		$frontend_controller = $container->get( FrontendController::class );
		$frontend_controller->register_hooks();
	}
}
