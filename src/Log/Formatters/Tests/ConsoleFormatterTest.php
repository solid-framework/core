<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Formatters\Tests;

use Chalk\Chalk;
use Chalk\Color;
use Chalk\Style;
use Psr\Log\LogLevel;
use PHPUnit\Framework\TestCase;
use Solid\Log\Formatters\ConsoleFormatter;

/**
 * @package Solid\Log\Formatters\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 * @coversDefaultClass Solid\Log\Formatters\ConsoleFormatter
 */
class ConsoleFormatterTest extends TestCase
{
    /**
     * @internal
     * @since 0.1.0
     * @var ConsoleFormatter
     */
    protected $formatter;

    /**
     * @api
     * @before
     * @since 0.1.0
     * @return void
     */
    public function setup()
    {
        $this->formatter = new ConsoleFormatter;
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
            'Solid\Log\Formatters\FormatterInterface',
            class_implements('Solid\Log\Formatters\ConsoleFormatter'),
            'Should implement formatter interface'
        );
    }

    /**
     * @api
     * @test
     * @covers ::format
     * @since 0.1.0
     * @return void
     */
    public function testNewline()
    {
        $message = $this->formatter->format(LogLevel::INFO, 'Log message');

        $this->assertTrue(
            strrpos($message, PHP_EOL) === strlen($message) - 1,
            'Should append a newline to the message'
        );
    }

    /**
     * @api
     * @test
     * @covers ::format
     * @since 0.1.0
     * @return void
     */
    public function testRed()
    {
        $style = new Style([Color::RED]);

        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::EMERGENCY, ''), Chalk::parse("{→}", [$style])),
            'Should use red color for error messages'
        );
        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::ALERT, ''), Chalk::parse("{→}", [$style])),
            'Should use red color for error messages'
        );
        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::CRITICAL, ''), Chalk::parse("{→}", [$style])),
            'Should use red color for error messages'
        );
        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::ERROR, ''), Chalk::parse("{→}", [$style])),
            'Should use red color for error messages'
        );
    }

    /**
     * @api
     * @test
     * @covers ::format
     * @since 0.1.0
     * @return void
     */
    public function testYellow()
    {
        $style = new Style([Color::YELLOW]);

        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::WARNING, ''), Chalk::parse("{→}", [$style])),
            'Should use yellow color for warning messages'
        );
        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::NOTICE, ''), Chalk::parse("{→}", [$style])),
            'Should use yellow color for warning messages'
        );
    }

    /**
     * @api
     * @test
     * @covers ::format
     * @since 0.1.0
     * @return void
     */
    public function testBlue()
    {
        $style = new Style([Color::BLUE]);

        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::INFO, ''), Chalk::parse("{→}", [$style])),
            'Should use blue color for info messages'
        );

        $this->assertSame(
            0,
            strpos($this->formatter->format(LogLevel::DEBUG, ''), Chalk::parse("{→}", [$style])),
            'Should use blue color for info messages'
        );

    }

    /**
     * @api
     * @test
     * @covers ::format
     * @since 0.1.0
     * @return void
     */
    public function testFormat()
    {
        $message = 'Log message';
        $style = new Style([Color::RED]);

        $this->assertSame(
            Chalk::parse("{→} {$message}", [$style]) . PHP_EOL,
            $this->formatter->format(LogLevel::EMERGENCY, $message),
            'Should format messages correctly'
        );
    }
}
