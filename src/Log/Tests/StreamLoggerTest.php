<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Tests;

use PHPUnit\Framework\TestCase;
use Solid\Log\StreamLogger;

/**
 * @package Solid\Log\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 * @coversDefaultClass Solid\Log\StreamLogger
 */
class StreamLoggerTest extends TestCase
{
    /**
     * @internal
     * @since 0.1.0
     * @var StreamLogger
     */
    protected $logger;

    /**
     * @internal
     * @since 0.1.0
     * @var string
     */
    protected $logFile = __DIR__ . '/Fixtures/non-existing-file.log';

    /**
     * @api
     * @before
     * @since 0.1.0
     * @return void
     */
    public function setup()
    {
        $this->logger = new StreamLogger;
    }

    /**
     * @api
     * @after
     * @since 0.1.0
     * @return void
     */
    public function teardown()
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
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
            class_implements('Solid\Log\StreamLogger'),
            'Should implement PSR logger interface'
        );
    }

    /**
     * @api
     * @test
     * @covers ::__construct
     * @since 0.1.0
     * @return void
     */
    public function testConstructor()
    {
        ob_start();
        $this->logger->info('test log');
        $output = ob_get_clean();

        $this->assertSame('test log', $output, 'Should use the output stream as default');
    }

    /**
     * @api
     * @test
     * @covers ::getStream
     * @since 0.1.0
     * @return void
     */
    public function testGetStream()
    {
        $this->assertSame('php://output', $this->logger->getStream(), 'Should return stream');
    }

    /**
     * @api
     * @test
     * @covers ::setStream
     * @since 0.1.0
     * @return void
     */
    public function testSetStream()
    {
        $this->logger->setStream('new-stream');
        $this->assertSame('new-stream', $this->logger->getStream(), 'Should be able to set the stream');
    }

    /**
     * @api
     * @test
     * @covers ::log
     * @expectedException Psr\Log\InvalidArgumentException
     * @since 0.1.0
     * @return void
     */
    public function testInvalidLogLevel()
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
    public function testContext()
    {
        ob_start();
        $this->logger->info('This is {an} interpolated {message}', [
            'an' => 'AN',
            'message' => 'new message',
            'test' => 'this should do nothing'
        ]);
        $output = ob_get_clean();

        $this->assertSame(
            'This is AN interpolated new message',
            $output,
            'Should interpolate message with the given context'
        );
    }

    /**
     * @api
     * @test
     * @covers ::log
     * @since 0.1.0
     * @return void
     */
    public function testFormatter()
    {
        $this->logger->setFormatter(new Fixtures\Formatter);

        ob_start();
        $this->logger->info('Log message');
        $output = ob_get_clean();

        $this->assertSame(
            '[info] Log message',
            $output,
            'Should use the given formatter'
        );
    }

    /**
     * @api
     * @test
     * @covers ::log
     * @since 0.1.0
     * @return void
     */
    public function testStreamUrl()
    {
        $this->assertFileNotExists(
            $this->logFile,
            'Should create a log file if the given stream is a file name and it does not already exist'
        );

        $this->logger = new StreamLogger($this->logFile);
        $this->logger->info('Log message');

        $this->assertFileExists(
            $this->logFile,
            'Should create a log file if the given stream is a file name and it does not already exist'
        );
        $this->assertStringEqualsFile(
            $this->logFile,
            'Log message',
            'Should create a log file if the given stream is a file name and it does not already exist'
        );

        $this->assertEmpty(error_get_last(), 'Should not throw or raise error if the given stream is unreachable');

        $this->logger->setStream('test://test.com');
        $this->logger->info('Log message');

        $this->assertNotEmpty(error_get_last(), 'Should not throw or raise error if the given stream is unreachable');
    }
}
