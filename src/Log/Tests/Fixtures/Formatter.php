<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Tests\Fixtures;

use Solid\Log\Formatters\FormatterInterface;

/**
 * @package Solid\Log\Tests\Fixtures
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Formatter implements FormatterInterface
{
    /**
     * @api
     * @since 0.1.0
     * @param string $level   The log level.
     * @param string $message The log message.
     * @return string
     */
    public function format(string $level, string $message): string
    {
        return "[{$level}] {$message}";
    }
}
