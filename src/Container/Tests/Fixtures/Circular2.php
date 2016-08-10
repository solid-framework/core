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
class Circular2
{
    /**
     * @internal
     * @since 0.1.0
     * @var Circular1
     */
    protected $circular1;

    /**
     * @api
     * @since 0.1.0
     * @param Circular1 $circular1 An instance of Circular1
     */
    public function __construct(Circular1 $circular1)
    {
        $this->circular2 = $circular1;
    }

    /**
     * @api
     * @since 0.1.0
     * @return Circular1
     */
    public function getDependency(): Circular1
    {
        return $this->circular1;
    }
}
