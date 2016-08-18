<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Config\Tests;

use Solid\Config\Config;
use PHPUnit\Framework\TestCase;

/**
 * @package Solid\Config\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class ConfigTest extends TestCase
{
    /**
     * @api
     * @test
     * @since 0.1.0
     * @return void
     */
    public function testConstructor()
    {
        $config = new Config;
        $this->assertEquals([], $config->get(), 'Should be empty if no settings are given');

        $config = new Config(['test' => 'value']);
        $this->assertEquals(['test' => 'value'], $config->get(), 'Should take settings through constructor');
    }

    /**
     * @api
     * @test
     * @since 0.1.0
     * @return void
     */
    public function testSet()
    {
        $config = new Config;
        $config->set(['test' => 'set']);

        $this->assertEquals(['test' => 'set'], $config->get(), 'Should be able to set settings');
    }

    /**
     * @api
     * @test
     * @since 0.1.0
     * @return void
     */
    public function testHas()
    {
        $config = new Config;
        $config->set(['test' => 'has']);

        $this->assertTrue($config->has('test'), 'Should be able to determine the existence of a field');
        $this->assertFalse($config->has('undefined'), 'Should be able to determine the existence of a field');
    }

    /**
     * @api
     * @test
     * @since 0.1.0
     * @return void
     */
    public function testGet()
    {
        $config = new Config(['test' => 'value', 'second' => ['test' => 'value 2']]);

        $this->assertEquals(
            ['test' => 'value', 'second' => ['test' => 'value 2']],
            $config->get(),
            'Should return all settings if no field is given'
        );
        $this->assertEquals('value', $config->get('test'), 'Should retrieve the given field value');
        $this->assertEquals('value 2', $config->get('second.test'), 'Should be able to retrieve nested values');
        $this->assertNull($config->get('undefined'), "Should return null if field doesn't exist");
        $this->assertEquals(
            'default',
            $config->get('undefined', 'default'),
            "Should return the given default value if field doesn't exist"
        );
    }

    /**
     * @api
     * @test
     * @since 0.1.0
     * @return void
     */
    public function testPut()
    {
        $config = new Config(['test' => 'value', 'second' => ['test' => 'value 2']]);

        $config->put('test', 'updated value');
        $this->assertEquals('updated value', $config->get('test'), 'Should be able to update field');

        $config->put('second.test', 'updated value 2');
        $this->assertEquals('updated value 2', $config->get('second.test'), 'Should be able to update nested field');

        $config->put('second.nested.test', 'new value 3');
        $this->assertEquals('new value 3', $config->get('second.nested.test'), 'Should create new fields if needed');
    }

    /**
     * @api
     * @test
     * @since 0.1.0
     * @return void
     */
    public function testMerge()
    {
        $config = new Config;

        $config->merge([
            'test-1' => 'value 1',
            'test-2' => [
                'nested' => 'value 2'
            ]
        ]);
        $this->assertEquals(
            [
                'test-1' => 'value 1',
                'test-2' => [
                    'nested' => 'value 2'
                ]
            ],
            $config->get(),
            'Should be able to merge in settings at root'
        );

        $config->merge([
            'test-2' => [
                'nested' => 'updated value 2',
                'nested-2' => 'new nested value 2'
            ],
            'test-3' => ['one', 'two', 'three']
        ]);
        $this->assertEquals(
            [
                'test-1' => 'value 1',
                'test-2' => [
                    'nested' => 'updated value 2',
                    'nested-2' => 'new nested value 2'
                ],
                'test-3' => ['one', 'two', 'three']
            ],
            $config->get(),
            'Should be able to recursively merge in settings at root'
        );

        $config->merge([
            'nested-2' => 'updated nested value 2',
            'nested-3' => 'new nested value 3'
        ], 'test-2');
        $this->assertEquals([
            'nested' => 'updated value 2',
            'nested-2' => 'updated nested value 2',
            'nested-3' => 'new nested value 3'
        ], $config->get('test-2'), 'Should be able to merge in settings with the given field');

        $config->set(['array' => ['one', 'two', 'three']]);
        $config->merge(['array' => ['four', 'five', 'six']]);
        $this->assertEquals(['array' => ['four', 'five', 'six']], $config->get(), 'Should override arrays by default');

        $config->set(['array' => ['one', 'two', 'three']]);
        $config->merge(['array' => ['four', 'five', 'six']], null, true);
        $this->assertEquals(
            [
                'array' => [
                    'one', 'two', 'three', 'four', 'five', 'six'
                ]
            ],
            $config->get(),
            'Should be able to merge indexed arrays'
        );

        $config->set(['array' => ['one', 'two', 'three']]);
        $config->merge(['array' => ['two', 'three', 'four']], null, true);
        $this->assertEquals(
            [
                'array' => [
                    'one', 'two', 'three', 'four'
                ]
            ],
            $config->get(),
            'Should only merge unique values'
        );
    }
}
