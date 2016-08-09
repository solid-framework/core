<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Tests;

use Solid\Kernel\Request as KernelRequest;
use Solid\Kernel\RequestInterface;

/**
 * @package Solid\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Request implements RequestInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param KernelRequest $request A kernel request to generate the implementation specific request from.
     * @return RequestInterface
     */
    public static function fromKernelRequest(KernelRequest $request): RequestInterface
    {
        // just return an empty request object
        return new self;
    }
}
