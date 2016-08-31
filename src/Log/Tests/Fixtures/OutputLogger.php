<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Tests\Fixtures;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

/**
 * @package Solid\Log\Tests\Fixtures
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class OutputLogger extends AbstractLogger
{
    use \Solid\Log\LoggerTrait;

    /**
     * @api
     * @since 0.1.0
     * @var array
     */
    const SUPPORTED_LOG_LEVELS = [
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    ];

    /**
     * @api
     * @since 0.1.0
     * @param string $level   The log level to use.
     * @param string $message The message to log.
     * @param array  $context The context to interpolate the message with.
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (!in_array($level, self::SUPPORTED_LOG_LEVELS)) {
            throw new InvalidArgumentException("Log level \"{$level}\" is not supported");
        }

        if (!empty($context)) {
            $message = $this->interpolate($message, $context);
        }

        echo $message . PHP_EOL;
    }
}
