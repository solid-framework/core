<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Formatters;

/**
 * @package Solid\Log\Formatters
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
interface FormatterInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param string $level   The log level.
     * @param string $message The log message.
     * @return string
     */
    public function format(string $level, string $message): string;
}
