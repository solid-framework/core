<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Tests;

use PHPUnit\Framework\TestCase;
use Solid\Application;

/**
 * @package Solid\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 * @coversDefaultClass Solid\Application
 */
class ApplicationTest extends TestCase
{
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
            'Solid\ApplicationInterface',
            class_implements('Solid\Application'),
            'Application should implement ApplicationInterface'
        );
        $this->assertArrayHasKey(
            'Solid\RunnableInterface',
            class_implements('Solid\Application'),
            'Application should implement RunnableInterface'
        );
        $this->assertArrayHasKey(
            'Solid\Container\ContainerInterface',
            class_implements('Solid\Application'),
            'Application should implement ContainerInterface'
        );
    }

    /**
     * @api
     * @test
     * @covers ::__construct
     * @covers ::getDirectory
     * @covers ::getSapiNamespace
     * @since 0.1.0
     * @return void
     */
    public function testConstructor()
    {
        // use default constructor parameters
        $application = new Application;

        $this->assertNotNull($application->getDirectory(), 'Application constructor should have a default directory');
        $this->assertNotNull($application->getSapiNamespace(), 'Application should determine a SAPI namespace');

        // use custom constructor parameters
        $application = new Application('/test/directory', 'TestSapiNamespace');

        $this->assertEquals(
            '/test/directory',
            $application->getDirectory(),
            'Application should accept custom directory'
        );
        $this->assertEquals(
            'TestSapiNamespace',
            $application->getSapiNamespace(),
            'Application should accept custom SAPI namespace'
        );
    }

    /**
     * @api
     * @test
     * @covers ::run
     * @expectedException \Solid\UnsupportedEnvironmentException
     * @since 0.1.0
     * @return void
     */
    public function testEnvironmentFail()
    {
        $application = new Application(__DIR__ . '/non/existing/path', 'NonExistingNamespace');
        $application->run();
    }

    /**
     * @api
     * @test
     * @covers ::run
     * @since 0.1.0
     * @return void
     */
    public function testRun()
    {
        $application = new Application(__DIR__ . '/Fixtures', 'Tests');
        $application->run();

        $this->assertTrue(true, 'Application should run without exceptions');
    }

    /**
     * @api
     * @test
     * @covers ::__construct
     * @since 0.1.0
     * @return void
     */
    public function testConfig()
    {
        $application = new Application(__DIR__ . '/Fixtures', 'Tests');

        $this->assertEquals(
            'value',
            $application->resolve('config')->get('key'),
            'The application configuration file should be loaded'
        );
    }

    /**
     * @api
     * @test
     * @covers ::__construct
     * @since 0.1.0
     * @return void
     */
    public function testLogger()
    {
        $application = new Application(__DIR__ . '/Fixtures', 'Tests');
        $logger = $application->resolve('logger');

        $this->assertInstanceOf('Solid\Log\Logger', $logger);

        $message = 'Log message';

        ob_start();
        $logger->info($message);
        $output = ob_get_clean();

        $this->assertNotFalse(
            strpos($output, $message),
            'Should load the output logger according to the configuration file'
        );
    }

    /**
     * @api
     * @test
     * @covers ::__construct
     * @since 0.1.0
     * @return void
     */
    public function testStartup()
    {
        $application = new Application(__DIR__ . '/Fixtures', 'Tests');

        $this->assertEquals('run', $application->resolve('testStartup'), 'The application Startup should be called');
    }
}
