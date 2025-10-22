<?php
/**
 * Dependency Injection Container
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Core;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Exception;

/**
 * Simple PSR-11 compliant dependency injection container.
 *
 * @since 1.0.0
 */
class Container implements ContainerInterface {

	/**
	 * Container bindings.
	 *
	 * @var array
	 */
	private $bindings = [];

	/**
	 * Resolved instances (singletons).
	 *
	 * @var array
	 */
	private $instances = [];

	/**
	 * Bind a service to the container.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $resolver Closure that resolves the service.
	 * @param bool     $shared   Whether the service should be a singleton.
	 * @return void
	 */
	public function bind( string $id, callable $resolver, bool $shared = false ): void {
		$this->bindings[ $id ] = [
			'resolver' => $resolver,
			'shared'   => $shared,
		];
	}

	/**
	 * Bind a singleton service to the container.
	 *
	 * @param string   $id       Service identifier.
	 * @param callable $resolver Closure that resolves the service.
	 * @return void
	 */
	public function singleton( string $id, callable $resolver ): void {
		$this->bind( $id, $resolver, true );
	}

	/**
	 * Bind an existing instance to the container.
	 *
	 * @param string $id       Service identifier.
	 * @param mixed  $instance Service instance.
	 * @return void
	 */
	public function instance( string $id, $instance ): void {
		$this->instances[ $id ] = $instance;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get( string $id ) {
		if ( $this->has( $id ) ) {
			return $this->resolve( $id );
		}

		throw new class( "Service '{$id}' not found in container." ) extends Exception implements NotFoundExceptionInterface {};
	}

	/**
	 * {@inheritDoc}
	 */
	public function has( string $id ): bool {
		return isset( $this->bindings[ $id ] ) || isset( $this->instances[ $id ] );
	}

	/**
	 * Resolve a service from the container.
	 *
	 * @param string $id Service identifier.
	 * @return mixed
	 * @throws Exception If service cannot be resolved.
	 */
	private function resolve( string $id ) {
		// Return existing instance if available.
		if ( isset( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		// Get binding configuration.
		$binding = $this->bindings[ $id ];
		$resolver = $binding['resolver'];
		$shared = $binding['shared'];

		// Resolve the service.
		try {
			$instance = $resolver( $this );
		} catch ( Exception $e ) {
			throw new class( "Error resolving service '{$id}': " . $e->getMessage(), 0, $e ) extends Exception implements ContainerExceptionInterface {};
		}

		// Store as singleton if shared.
		if ( $shared ) {
			$this->instances[ $id ] = $instance;
		}

		return $instance;
	}

	/**
	 * Automatically resolve a class with its dependencies.
	 *
	 * @param string $class Class name to resolve.
	 * @return object
	 * @throws Exception If class cannot be resolved.
	 */
	public function make( string $class ) {
		if ( $this->has( $class ) ) {
			return $this->get( $class );
		}

		if ( ! class_exists( $class ) ) {
			throw new Exception( "Class '{$class}' does not exist." );
		}

		$reflector = new \ReflectionClass( $class );

		if ( ! $reflector->isInstantiable() ) {
			throw new Exception( "Class '{$class}' is not instantiable." );
		}

		$constructor = $reflector->getConstructor();

		// Class has no constructor - instantiate directly.
		if ( null === $constructor ) {
			return new $class();
		}

		$parameters = $constructor->getParameters();
		$dependencies = [];

		foreach ( $parameters as $parameter ) {
			$type = $parameter->getType();

			if ( null === $type || $type->isBuiltin() ) {
				// Handle primitive types or no type hint.
				if ( $parameter->isDefaultValueAvailable() ) {
					$dependencies[] = $parameter->getDefaultValue();
				} else {
					throw new Exception( "Cannot resolve parameter '{$parameter->getName()}' for class '{$class}'." );
				}
			} else {
				// Resolve class dependency.
				$dependency_class = $type->getName();
				$dependencies[] = $this->make( $dependency_class );
			}
		}

		return $reflector->newInstanceArgs( $dependencies );
	}
}
