<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Container;

use ReflectionClass;
use ReflectionException;

/**
 * @package Solid\Container
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Container implements ContainerInterface
{
    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $bindings = [];

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $instances = [];

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $aliases = [];

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $dependencyStack = [];

    /**
     * @api
     * @since 0.1.0
     * @param string        $abstract The abstract to bind to.
     * @param callable|null $factory  The factory to bind to the abstract.
     * @param bool          $shared   Whether to share the resolved instance.
     * @return void
     */
    public function bind(string $abstract, callable $factory = null, bool $shared = false)
    {
        $concrete = $factory ?? $abstract;

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to bind the instance to.
     * @param mixed  $instance The instance to bind to the abstract.
     * @return void
     */
    public function instance(string $abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * @api
     * @since 0.1.0
     * @param string      $abstract      The abstract to resolve.
     * @param mixed|null  ...$parameters An array of parameters to pass to the abstract factory.
     * @return mixed
     * @throws DependencyResolutionException
     */
    public function resolve(string $abstract, ...$parameters)
    {
        $abstract = $this->resolveAlias($abstract);

        // watch for circular dependencies
        if (in_array($abstract, $this->dependencyStack)) {
            throw new DependencyResolutionException("The dependency \"{$abstract}\" is circular");
        }

        // if the given abstract is cached we return it
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        // push the abstract to the dependency stack to watch for circular dependencies
        $this->dependencyStack[] = $abstract;

        $concrete = $this->getConcrete($abstract);
        $result = is_callable($concrete) || $concrete === $abstract ?
            $this->construct($concrete, $parameters) :
            $this->resolve($concrete, $parameters);

        // remove the resolved abstract from the dependency stack
        unset($this->dependencyStack[array_search($abstract, $this->dependencyStack)]);

        // if the abstract is shared cache it
        if ($this->isShared($abstract)) {
            $this->instance($abstract, $result);
        }

        return $result;
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to alias.
     * @param string $alias    The alias to use.
     * @return void
     * @throws AliasNotAvailableException
     */
    public function alias(string $abstract, string $alias)
    {
        if ($this->isAlias($alias)) {
            throw new AliasNotAvailableException("Alias \"{$alias}\" already exists");
        }

        $this->aliases[$alias] = $abstract;
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $alias The alias to check for.
     * @return bool
     */
    public function isAlias(string $alias): bool
    {
        return array_key_exists($alias, $this->aliases);
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to check for.
     * @return bool
     */
    public function isBound(string $abstract): bool
    {
        $abstract = $this->resolveAlias($abstract);

        return array_key_exists($abstract, $this->instances) || array_key_exists($abstract, $this->bindings);
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to check for.
     * @return bool
     */
    public function isShared(string $abstract): bool
    {
        $abstract = $this->resolveAlias($abstract);

        return
            array_key_exists($abstract, $this->instances) ||
            (array_key_exists($abstract, $this->bindings) && $this->bindings[$abstract]['shared']);
    }

    /**
     * @internal
     * @since 0.1.0
     * @param string $alias
     * @return string
     */
    protected function resolveAlias(string $alias): string
    {
        return $this->aliases[$alias] ?? $alias;
    }

    /**
     * @internal
     * @since 0.1.0
     * @param string $abstract
     * @return callable|string
     */
    protected function getConcrete(string $abstract)
    {
        return array_key_exists($abstract, $this->bindings) ? $this->bindings[$abstract]['concrete'] : $abstract;
    }

    /**
     * @internal
     * @since 0.1.0
     * @param callable|string $concrete
     * @param array           $parameters
     * @return mixed
     * @throws ResolveException
     * @throws NonInstantiableClassException
     */
    protected function construct($concrete, array $parameters = [])
    {
        // if the concrete is a factory we return it passing in the container and the parameters
        if (is_callable($concrete)) {
            return $concrete($this, ...$parameters);
        }

        try {
            $reflection = new ReflectionClass($concrete);
        } catch (ReflectionException $exception) {
            throw new ResolveException("Class {$concrete} does not exist");
        }

        if (!$reflection->isInstantiable()) {
            throw new NonInstantiableClassException("\"{$concrete}\" is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return $reflection->newInstance();
        }

        // resolve required constructor dependencies
        $constructorParameters = $constructor->getParameters();
        $resolvedDependencies = $this->resolveConstructorDependencies($constructorParameters, $parameters);

        return $reflection->newInstanceArgs($resolvedDependencies);
    }

    /**
     * @internal
     * @since 0.1.0
     * @param array $constructorParameters
     * @param array $passedParameters
     * @return array
     * @throws DependencyResolutionException
     */
    protected function resolveConstructorDependencies(array $constructorParameters, array $passedParameters): array
    {
        $resolvedDependencies = [];

        foreach ($constructorParameters as $parameter) {
            $parameterPosition = $parameter->getPosition();

            // use the passed parameter if available
            if (array_key_exists($parameterPosition, $passedParameters)) {
                $resolvedDependencies[$parameterPosition] = $passedParameters[$parameterPosition];
            } elseif ($parameter->isOptional()) {
                $resolvedDependencies[$parameterPosition] = $parameter->getDefaultValue();
            } else {
                // try to resolve the required dependency
                $dependencyClass = $parameter->getClass();

                if (is_null($dependencyClass)) {
                    $declaringClass = $parameter->getDeclaringClass()->getName();
                    $parameterName = $parameter->getName();

                    throw new DependencyResolutionException(
                        "Unresolved dependency: \${$parameterName} in {$declaringClass}::__constructor"
                    );
                }

                $resolvedDependencies[$parameterPosition] = $this->resolve($dependencyClass->name);
            }
        }

        return $resolvedDependencies;
    }
}
