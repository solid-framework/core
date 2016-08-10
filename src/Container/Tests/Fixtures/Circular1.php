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
class Circular1
{
    /**
     * @internal
     * @since 0.1.0
     * @var Circular2
     */
    protected $circular2;

    /**
     * @api
     * @since 0.1.0
     * @param Circular2 $circular2 An instance of Circular2
     */
    public function __construct(Circular2 $circular2)
    {
        $this->circular2 = $circular2;
    }

    /**
     * @api
     * @since 0.1.0
     * @return Circular2
     */
    public function getDependency(): Circular2
    {
        return $this->circular2;
    }
}
