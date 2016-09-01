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
use Psr\Log\InvalidArgumentException;

/**
 * @package Solid\Log
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class StreamLogger extends AbstractLogger
{
    use LoggerTrait;

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
     * @var string|resource
     */
    protected $stream;

    /**
     * @api
     * @since 0.1.0
     * @param string|resource $stream The stream or stream url to log to.
     */
    public function __construct($stream = 'php://output')
    {
        $this->stream = $stream;
    }

    /**
     * @api
     * @since 0.1.0
     * @param string|resource $stream The stream or stream url to use.
     * @return void
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @api
     * @since 0.1.0
     * @return string|resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $level   The log level to use.
     * @param string $message The message to log.
     * @param array  $context The context to interpolate the message with.
     * @return void
     * @throws InvalidArgumentException
     */
    public function log($level, $message, array $context = [])
    {
        if (!in_array($level, self::SUPPORTED_LOG_LEVELS)) {
            throw new InvalidArgumentException("Log level \"{$level}\" is not supported");
        }

        if (!empty($context)) {
            $message = $this->interpolate($message, $context);
        }

        // don't throw or raise errors if the stream isn't writeable
        if (($handle = @fopen($this->stream, 'a')) !== false) {
            if (!is_null($this->formatter)) {
                $message = $this->formatter->format($level, $message);
            }

            fwrite($handle, $message, strlen($message));
            fclose($handle);
        }
    }
}
