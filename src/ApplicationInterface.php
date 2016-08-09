<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid;

/**
 * @package Solid
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
interface ApplicationInterface extends RunnableInterface, Container\ContainerInterface
{
    /**
     * @api
     * @since 0.1.0
     * @return string
     */
    public function getSapiNamespace(): string;

    /**
     * @api
     * @since 0.1.0
     * @return string
     */
    public function getDirectory(): string;
}
