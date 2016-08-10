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
class B
{
    /**
     * @internal
     * @since 0.1.0
     * @var C
     */
    protected $c;

    /**
     * @api
     * @since 0.1.0
     * @param C $c An instance of C
     */
    public function __construct(C $c)
    {
        $this->c = $c;
    }

    /**
     * @api
     * @since 0.1.0
     * @return C
     */
    public function getDependency(): C
    {
        return $this->c;
    }
}
