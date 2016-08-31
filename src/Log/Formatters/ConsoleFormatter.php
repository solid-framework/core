<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Formatters;

use Chalk\Chalk;
use Chalk\Color;
use Chalk\Style;

/**
 * @package Solid\Log\Formatters
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class ConsoleFormatter implements FormatterInterface
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
        $style = new Style;

        switch ($level) {
            case 'emergency':
            case 'alert':
            case 'critical':
            case 'error':
                $style->setStyle(Color::RED);
                break;
            case 'warning':
            case 'notice':
                $style->setStyle(Color::YELLOW);
                break;
            case 'info':
            case 'debug':
                $style->setStyle(Color::BLUE);
                break;
        }

        return Chalk::parse("{→} {$message}", [$style]) . PHP_EOL;
    }
}
