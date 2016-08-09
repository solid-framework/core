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
interface KernelInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param RequestInterface $request The request to handle.
     * @return ResponseInterface
     * @throws UnsupportedRequestTypeException
     */
    public function handleRequest(RequestInterface $request): ResponseInterface;

    /**
     * @api
     * @since 0.1.0
     * @param ResponseInterface $response The response to dispatch.
     * @return void
     * @throws UnsupportedResponseTypeException
     */
    public function dispatchResponse(ResponseInterface $response);
}
