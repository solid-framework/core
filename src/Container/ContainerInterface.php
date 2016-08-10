<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Container;

/**
 * @package Solid\Container
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
interface ContainerInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param string        $abstract The abstract to bind.
     * @param callable|null $factory  The factory to bind to the abstract.
     * @param bool          $shared   Whether to share the resolved instance.
     * @return void
     */
    public function bind(string $abstract, callable $factory = null, bool $shared = false);

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to bind the instance to.
     * @param mixed  $instance The instance to bind to the abstract.
     * @return void
     */
    public function instance(string $abstract, $instance);

    /**
     * @api
     * @since 0.1.0
     * @param string      $abstract      The abstract to resolve.
     * @param mixed|null  ...$parameters An array of parameters to pass to the abstract factory.
     * @return mixed
     * @throws DependencyResolutionException
     */
    public function resolve(string $abstract, ...$parameters);

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to alias.
     * @param string $alias    The alias to use.
     * @return void
     */
    public function alias(string $abstract, string $alias);

    /**
     * @api
     * @since 0.1.0
     * @param string $alias The alias to check for.
     * @return bool
     */
    public function isAlias(string $alias): bool;

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to check for.
     * @return bool
     */
    public function isBound(string $abstract): bool;

    /**
     * @api
     * @since 0.1.0
     * @param string $abstract The abstract to check for.
     * @return bool
     */
    public function isShared(string $abstract): bool;
}
