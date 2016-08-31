<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log\Tests;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Solid\Log\LoggerTrait;

/**
 * @package Solid\Log\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 * @coversDefaultClass Solid\Log\LoggerTrait
 */
class LoggerTraitTest extends TestCase
{
    /**
     * @internal
     * @since 0.1.0
     * @var Fixtures\LoggerTrait
     */
    protected $loggerTrait;

    /**
     * @api
     * @before
     * @since 0.1.0
     * @return void
     */
    public function setup()
    {
        $this->loggerTrait = new Fixtures\LoggerTrait;
    }

    /**
     * @api
     * @test
     * @covers ::interpolate
     * @since 0.1.0
     * @return void
     */
    public function testInterpolate()
    {
        $reflection = new ReflectionClass($this->loggerTrait);

        $interpolate = $reflection->getMethod('interpolate');
        $interpolate->setAccessible(true);

        $this->assertSame(
            'This is a test string',
            $interpolate->invoke(
                $this->loggerTrait,
                'This is a test string',
                [
                    'test' => 'Should do nothing'
                ]
            ),
            'Should not alter the string if no replacements are found'
        );

        $this->assertSame(
            'This is a {test} string',
            $interpolate->invoke(
                $this->loggerTrait,
                'This is a {test} string'
            ),
            'Should not alter the string if no context is given'
        );

        $this->assertSame(
            'This is AN interpolated test message',
            $interpolate->invoke($this->loggerTrait, 'This is {an} interpolated {message}', [
                'an' => 'AN',
                'message' => 'test message',
                'test' => 'should do nothing'
            ]),
            'Should interpolate message with the given context'
        );

        $this->assertSame(
            'This is {a} test new string',
            $interpolate->invoke(
                $this->loggerTrait,
                'This is {a} test {string}',
                [
                    'a' => [
                        'test' => 'test'
                    ],
                    'string' => 'new string'
                ]
            ),
            'Should not interpolate non stringable values'
        );
    }

    /**
     * @api
     * @test
     * @covers ::getFormatter
     * @covers ::setFormatter
     * @since 0.1.0
     * @return void
     */
    public function testFormatter()
    {
        $this->assertNull($this->loggerTrait->getFormatter(), 'Should return null if no formatter is set');

        $formatter = new Fixtures\Formatter;
        $this->loggerTrait->setFormatter($formatter);

        $this->assertSame($formatter, $this->loggerTrait->getFormatter(), 'Should set the given formatter');
    }
}
