<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Config\Tests;

use Solid\Config\Config;
use Solid\Config\ConfigSection;
use PHPUnit\Framework\TestCase;

/**
 * @package Solid\Config\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 * @coversDefaultClass Solid\Config\ConfigSection
 */
class ConfigSectionTest extends TestCase
{
    /**
     * @internal
     * @since 0.1.0
     * @var Config
     */
    protected $config;

    /**
     * @internal
     * @since 0.1.0
     * @var ConfigSection
     */
    protected $configSection;

    /**
     * @api
     * @before
     * @since 0.1.0
     * @return void
     */
    public function setup()
    {
        $this->config = new Config;
        $this->configSection = new ConfigSection('test', $this->config);
    }

    /**
     * @api
     * @test
     * @coversNothing
     * @since 0.1.0
     * @return void
     */
    public function testInstanceof()
    {
        $this->assertInstanceOf('Solid\Config\Config', $this->configSection, 'Should extend Config');
    }

    /**
     * @api
     * @test
     * @coversNothing
     * @since 0.1.0
     * @return void
     */
    public function testPrefix()
    {
        $this->assertArrayHasKey('test', $this->config->get());
    }

    /**
     * @api
     * @test
     * @covers ::set
     * @since 0.1.0
     * @return void
     */
    public function testSet()
    {
        $this->configSection->set(['test' => 'value']);

        $this->assertEquals(
            ['test' => 'value'],
            $this->configSection->get(),
            'Should be able to retrieve section values'
        );

        // test the config reference
        $this->assertEquals(
            [
                'test' => [
                    'test' => 'value'
                ]
            ],
            $this->config->get(),
            'Should affect the config object reference'
        );
    }

    /**
     * @api
     * @test
     * @covers ::has
     * @since 0.1.0
     * @return void
     */
    public function testHas()
    {
        $this->configSection->set(['test' => 'value']);

        $this->assertTrue($this->configSection->has('test'), 'Should be able to determine the existence of a field');
        $this->assertFalse($this->configSection->has('undefined'), 'Should be able to determine the existence of a field');
    }

    /**
     * @api
     * @test
     * @covers ::get
     * @since 0.1.0
     * @return void
     */
    public function testGet()
    {
        $this->configSection->set([
            'test' => 'value 1',
            'nested' => [
                'test' => 'value 2'
            ]
        ]);

        $this->assertEquals('value 1', $this->configSection->get('test'), 'Should be able to retrieve values');
        $this->assertEquals('value 2', $this->configSection->get('nested.test'), 'Should be able to retrieve nested values');
    }

    /**
     * @api
     * @test
     * @covers ::put
     * @since 0.1.0
     * @return void
     */
    public function testPut()
    {
        $this->configSection->set([
            'test' => 'value',
            'nested' => [
                'test' => 'value'
            ]
        ]);

        $this->configSection->put('test', 'updated value');
        $this->assertEquals(
            [
                'test' => 'updated value',
                'nested' => [
                    'test' => 'value'
                ]
            ],
            $this->configSection->get(),
            'Should be able to update values'
        );

        $this->configSection->put('nested.test', 'updated nested value');
        $this->assertEquals(
            [
                'test' => 'updated value',
                'nested' => [
                    'test' => 'updated nested value'
                ]
            ],
            $this->configSection->get(),
            'Should be able to update nested values'
        );
    }

    /**
     * @api
     * @test
     * @covers ::merge
     * @since 0.1.0
     * @return void
     */
    public function testMerge()
    {
        $this->configSection->set([
            'test-1' => ['value 1'],
            'test-2' => [
                'nested' => [
                    'test' => 'value 2'
                ]
            ]
        ]);

        $this->configSection->merge([
            'test-2' => [
                'nested' => [
                    'test' => 'updated value 2'
                ],
                'nested-2' => 'new value'
            ],
            'test-3' => 'value 3'
        ]);
        $this->assertEquals(
            [
                'test-1' => ['value 1'],
                'test-2' => [
                    'nested' => [
                        'test' => 'updated value 2'
                    ],
                    'nested-2' => 'new value'
                ],
                'test-3' => 'value 3'
            ],
            $this->configSection->get(),
            'Should be able to recursively merge values'
        );

        $this->configSection->merge([
            'nested' => [
                'test' => 'updated value 2',
                'test-2' => 'new nested value 2'
            ]
        ], 'test-2');
        $this->assertEquals(
            [
                'test-1' => ['value 1'],
                'test-2' => [
                    'nested' => [
                        'test' => 'updated value 2',
                        'test-2' => 'new nested value 2'
                    ],
                    'nested-2' => 'new value'
                ],
                'test-3' => 'value 3'
            ],
            $this->configSection->get(),
            'Should be able to merge in settings with the given field'
        );
    }
}
