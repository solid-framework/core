<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Kernel;

/**
 * @package Solid\Kernel
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
interface RequestInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param Request $request A kernel request to generate the implementation specific request from.
     * @return RequestInterface
     */
    public static function fromKernelRequest(Request $request): self;
}
