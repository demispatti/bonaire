<?php
/**
 * Main Plugin Class
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Core;

use Bonaire\Core\Container;
use Bonaire\Infrastructure\ServiceProviders\CoreServiceProvider;
use Bonaire\Infrastructure\ServiceProviders\AdminServiceProvider;
use Bonaire\Infrastructure\ServiceProviders\FrontendServiceProvider;

/**
 * Main plugin orchestrator class.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Plugin text domain.
	 *
	 * @var string
	 */
	private $domain;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Dependency injection container.
	 *
	 * @var Container
	 */
	private $container;

	/**
	 * Service providers.
	 *
	 * @var array
	 */
	private $providers = [];

	/**
	 * Plugin constructor.
	 *
	 * @param string $name    Plugin name.
	 * @param string $domain  Plugin text domain.
	 * @param string $version Plugin version.
	 */
	public function __construct( string $name, string $domain, string $version ) {
		$this->name = $name;
		$this->domain = $domain;
		$this->version = $version;
		$this->container = new Container();
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function initialize(): void {
		$this->register_core_bindings();
		$this->register_providers();
		$this->boot_providers();
	}

	/**
	 * Register core services in the container.
	 *
	 * @return void
	 */
	private function register_core_bindings(): void {
		$this->container->instance( 'plugin.name', $this->name );
		$this->container->instance( 'plugin.domain', $this->domain );
		$this->container->instance( 'plugin.version', $this->version );
		$this->container->instance( Container::class, $this->container );
	}

	/**
	 * Register all service providers.
	 *
	 * @return void
	 */
	private function register_providers(): void {
		$this->providers = [
			CoreServiceProvider::class,
			AdminServiceProvider::class,
			FrontendServiceProvider::class,
		];

		foreach ( $this->providers as $provider_class ) {
			$provider = new $provider_class();
			$provider->register( $this->container );
		}
	}

	/**
	 * Bootstrap all service providers.
	 *
	 * @return void
	 */
	private function boot_providers(): void {
		foreach ( $this->providers as $provider_class ) {
			$provider = new $provider_class();
			$provider->boot( $this->container );
		}
	}

	/**
	 * Get the container instance.
	 *
	 * @return Container
	 */
	public function container(): Container {
		return $this->container;
	}

	/**
	 * Get plugin name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Get plugin domain.
	 *
	 * @return string
	 */
	public function domain(): string {
		return $this->domain;
	}

	/**
	 * Get plugin version.
	 *
	 * @return string
	 */
	public function version(): string {
		return $this->version;
	}
}
