<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Tests;

use Solid\Kernel\KernelInterface;
use Solid\Kernel\RequestInterface;
use Solid\Kernel\ResponseInterface;
use Solid\Kernel\UnsupportedRequestTypeException;
use Solid\Kernel\UnsupportedResponseTypeException;

/**
 * @package Solid\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Kernel implements KernelInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param RequestInterface $request The request to handle.
     * @return ResponseInterface
     * @throws UnsupportedRequestTypeException
     */
    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        // just return a dummy response object
        return new Response;
    }

    /**
     * @api
     * @since 0.1.0
     * @param ResponseInterface $response The response to dispatch.
     * @return void
     * @throws UnsupportedResponseTypeException
     */
    public function dispatchResponse(ResponseInterface $response)
    {
        // no need to do anything here
    }
}
