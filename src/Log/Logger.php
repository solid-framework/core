<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;

/**
 * @package Solid\Log
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Logger extends AbstractLogger
{
    /**
     * @api
     * @since 0.1.0
     * @var array
     */
    const SUPPORTED_LOG_LEVELS = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    ];

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $loggers = [];

    /**
     * @api
     * @since 0.1.0
     * @param LoggerInterface $logger The logger to add.
     * @param array           $levels The levels to add the logger to.
     * @return void
     */
    public function addLogger(LoggerInterface $logger, array $levels = null)
    {
        if (is_null($levels)) {
            $levels = self::SUPPORTED_LOG_LEVELS;
        }

        foreach ($levels as $level) {
            if (!in_array($level, self::SUPPORTED_LOG_LEVELS)) {
                throw new InvalidArgumentException("Log level \"{$level}\" is not supported");
            }

            if (!array_key_exists($level, $this->loggers)) {
                $this->loggers[$level] = [];
            }

            $this->loggers[$level][] = $logger;
        }
    }

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

        if (array_key_exists($level, $this->loggers) && !empty($this->loggers[$level])) {
            foreach ($this->loggers[$level] as $logger) {
                // don't throw exceptions if the registered logger doesn't support the log level
                try {
                    $logger->log($level, $message, $context);
                } catch (InvalidArgumentException $exception) {}
            }
        }
    }
}
