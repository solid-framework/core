<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Container\Tests\Fixtures;

/**
 * @package Solid\Container\Tests\Fixtures
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class A
{
    /**
     * @internal
     * @since 0.1.0
     * @var B
     */
    protected $b;

    /**
     * @api
     * @since 0.1.0
     * @param B $b An instance of B
     */
    public function __construct(B $b)
    {
        $this->b = $b;
    }

    /**
     * @api
     * @since 0.1.0
     * @return B
     */
    public function getDependency(): B
    {
        return $this->b;
    }
}
