<?php
/**
 * Admin Service Provider
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Infrastructure\ServiceProviders;

use Bonaire\Core\ServiceProvider;
use Bonaire\Core\Container;
use Bonaire\Admin\Services\OptionsService;
use Bonaire\Admin\Services\AssetService;
use Bonaire\Admin\Controllers\AdminController;
use Bonaire\Admin\Controllers\SettingsController;
use Bonaire\Admin\Controllers\AjaxController;
use Bonaire\Admin\Controllers\DashboardWidgetController;
use Bonaire\Admin\Controllers\MetaBoxController;

/**
 * Service provider for admin area functionality.
 *
 * @since 1.0.0
 */
class AdminServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		if ( ! is_admin() ) {
			return;
		}

		$this->register_services( $container );
		$this->register_controllers( $container );
	}

	/**
	 * Register admin services.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	private function register_services( Container $container ): void {
		// Options service.
		$container->singleton(
			OptionsService::class,
			fn( Container $c ) => new OptionsService( $c->get( 'plugin.domain' ) )
		);

		// Asset service.
		$container->singleton(
			AssetService::class,
			fn( Container $c ) => new AssetService(
				$c->get( 'plugin.version' ),
				BONAIRE_ROOT_URL
			)
		);
	}

	/**
	 * Register admin controllers.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	private function register_controllers( Container $container ): void {
		$container->singleton( AdminController::class, fn( Container $c ) => $c->make( AdminController::class ) );
		$container->singleton( SettingsController::class, fn( Container $c ) => $c->make( SettingsController::class ) );
		$container->singleton( AjaxController::class, fn( Container $c ) => $c->make( AjaxController::class ) );
		$container->singleton( DashboardWidgetController::class, fn( Container $c ) => $c->make( DashboardWidgetController::class ) );
		$container->singleton( MetaBoxController::class, fn( Container $c ) => $c->make( MetaBoxController::class ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot( Container $container ): void {
		if ( ! is_admin() ) {
			return;
		}

		// Boot admin controller.
		$admin_controller = $container->get( AdminController::class );
		$admin_controller->register_hooks();
	}
}
