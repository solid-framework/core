<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Tests;

use PHPUnit\Framework\TestCase;
use Solid\Log\Logger;
use Solid\Log\StreamLogger;
use Psr\Log\LogLevel;

/**
 * @package Solid\Log\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 * @coversDefaultClass Solid\Log\Logger
 */
class LoggerTest extends TestCase
{
    /**
     * @internal
     * @since 0.1.0
     * @var Logger
     */
    protected $logger;

    /**
     * @api
     * @before
     * @since 0.1.0
     * @return void
     */
    public function setup()
    {
        $this->logger = new Logger;
    }

    /**
     * @api
     * @test
     * @coversNothing
     * @since 0.1.0
     * @return void
     */
    public function testImplementationRequirements()
    {
        $this->assertArrayHasKey(
            'Psr\Log\LoggerInterface',
            class_implements($this->logger),
            'Should implement PSR logger interface'
        );
    }

    /**
     * @api
     * @test
     * @covers ::log
     * @since 0.1.0
     * @return void
     */
    public function testNoLoggers()
    {
        $this->logger->info('Log message');
        $this->assertTrue(true, 'Should not throw or raise exceptions if no loggers are registered');
    }

    /**
     * @api
     * @test
     * @covers ::addLogger
     * @covers ::log
     * @since 0.1.0
     * @return void
     */
    public function testAddLogger()
    {
        $this->logger->addLogger(new Fixtures\OutputLogger);

        ob_start();
        $this->logger->info('Log message');
        $output = ob_get_clean();

        $this->assertSame('Log message' . PHP_EOL, $output, 'Should use registered loggers');

        $this->logger->addLogger(new Fixtures\OutputLogger);

        ob_start();
        $this->logger->info('Log message');
        $output = ob_get_clean();

        $this->assertSame('Log message' . PHP_EOL . 'Log message' . PHP_EOL, $output, 'Should use registered loggers');
    }

    /**
     * @api
     * @test
     * @covers ::addLogger
     * @covers ::log
     * @since 0.1.0
     * @return void
     */
    public function testLoggerLevels()
    {
        $this->logger->addLogger(new Fixtures\OutputLogger, [LogLevel::DEBUG, LogLevel::INFO]);

        ob_start();
        $this->logger->debug('Log message');
        $output = ob_get_clean();

        $this->assertSame('Log message' . PHP_EOL, $output, 'Should use registered loggers at the correct level');

        ob_start();
        $this->logger->info('Log message');
        $output = ob_get_clean();

        $this->assertSame('Log message' . PHP_EOL, $output, 'Should use registered loggers at the correct level');

        ob_start();
        $this->logger->warning('Log message');
        $output = ob_get_clean();

        $this->assertSame('', $output, 'Should use registered loggers at the correct level');

        $this->logger->addLogger(new Fixtures\OutputLogger);

        ob_start();
        $this->logger->info('Log message');
        $output = ob_get_clean();

        $this->assertSame(
            'Log message' . PHP_EOL .
            'Log message' . PHP_EOL,
            $output,
            'Should use registered loggers at the correct level'
        );
    }

    /**
     * @api
     * @test
     * @covers ::addLogger
     * @expectedException Psr\Log\InvalidArgumentException
     * @since 0.1.0
     * @return void
     */
    public function testInvalidLogLevelsInAddLogger()
    {
        $this->logger->addLogger(new Fixtures\OutputLogger, ['unsupported']);
    }

    /**
     * @api
     * @test
     * @covers ::log
     * @expectedException Psr\Log\InvalidArgumentException
     * @since 0.1.0
     * @return void
     */
    public function testInvalidLogLevelsInLog()
    {
        $this->logger->log('unsupported', 'Log message');
    }

    /**
     * @api
     * @test
     * @covers ::log
     * @since 0.1.0
     * @return void
     */
    public function testInvalidLoggerLevels()
    {
        $this->logger->addLogger(new Fixtures\OutputLogger);

        ob_start();
        $this->logger->critical('Log message');
        $output = ob_get_clean();

        $this->assertSame(
            '',
            $output,
            'Should not throw or raise errors if registered loggers do not support the log level'
        );
    }
}
