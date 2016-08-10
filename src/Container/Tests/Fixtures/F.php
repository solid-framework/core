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
class F
{
    /**
     * @internal
     * @since 0.1.0
     * @var A
     */
    protected $a;

    /**
     * @internal
     * @since 0.1.0
     * @var string
     */
    protected $string;

    /**
     * @internal
     * @since 0.1.0
     * @var int
     */
    protected $number;

    /**
     * @api
     * @since 0.1.0
     * @param A        $a      An instance of A.
     * @param string   $string A string.
     * @param int|null $number A number.
     */
    public function __construct(A $a, string $string, int $number = 0)
    {
        $this->a = $a;
        $this->string = $string;
        $this->number = $number;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            $this->a, $this->string, $this->number
        ];
    }
}
