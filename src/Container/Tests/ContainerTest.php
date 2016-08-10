<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Container\Tests;

use Exception;
use Solid\Container\Container;
use PHPUnit_Framework_TestCase;

/**
 * @package Solid\Container\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @internal
     * @since 0.1.0
     * @var container
     */
    protected $container;

    /**
     * @internal
     * @since 0.1.0
     * @var int
     */
    protected $stringFactoryInvocations;

    /**
     * @api
     * @since 0.1.0
     * @before
     */
    public function setup()
    {
        $this->container = new Container;
        $this->stringFactoryInvocations = 0;
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testImplementationRequirements()
    {
        $this->assertArrayHasKey(
            'Solid\Container\ContainerInterface',
            class_implements('Solid\Container\Container'),
            'Container should implement ContainerInterface'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testBindFactory()
    {
        $this->container->bind('test', [$this, 'stringFactory']);

        $this->assertEquals(
            'Container test',
            $this->container->resolve('test'),
            'Should be able to resolve bound factories'
        );

        $this->container->resolve('test');
        $this->container->resolve('test');

        $this->assertEquals(
            3,
            $this->stringFactoryInvocations,
            'Unshared factory should be run every time it is resolved'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testBindSharedFactory()
    {
        $this->container->bind('test', [$this, 'stringFactory'], true);

        $this->assertEquals(
            'Container test',
            $this->container->resolve('test'),
            'Should be able to resolve bound factories'
        );

        $this->container->resolve('test');
        $this->container->resolve('test');

        $this->assertEquals(
            1,
            $this->stringFactoryInvocations,
            'Shared factory should NOT run every time it is resolved'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testBindSharedClass()
    {
        $this->container->bind('Solid\Container\Tests\Fixtures\C', null, true);
        $c = $this->container->resolve('Solid\Container\Tests\Fixtures\C');
        $this->assertInstanceOf('Solid\Container\Tests\Fixtures\C', $c, 'Should be able to resolve classes');

        $c2 = $this->container->resolve('Solid\Container\Tests\Fixtures\C');
        $this->assertSame($c, $c2, 'Shared classes should NOT be instantiated every time they are resolved');
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testFactoryParameters()
    {
        $this->container->bind('test', [$this, 'parameterFactory']);

        $parameters = $this->container->resolve('test', 'test', true);
        $this->assertEquals(
            [
                $this->container,
                'test',
                true
            ],
            $parameters,
            'The factory should receive an instance of the container and the parameters passed into resolve'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testInstance()
    {
        $this->container->instance('test', 'Container test');

        $this->assertEquals(
            'Container test',
            $this->container->resolve('test'),
            'Should be able to store and resolve instances'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testResolve()
    {
        $c = $this->container->resolve('Solid\Container\Tests\Fixtures\C');
        $this->assertInstanceOf('Solid\Container\Tests\Fixtures\C', $c, 'Should be able to resolve classes');

        $c2 = $this->container->resolve('Solid\Container\Tests\Fixtures\C');
        $this->assertNotSame($c, $c2, 'Unshared classes SHOULD be instantiated every time they are resolved');
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     * @expectedException \Solid\Container\DependencyResolutionException
     * @expectedExceptionMessageRegExp /is circular$/
     */
    public function testCircularDependencies()
    {
        $this->container->resolve('Solid\Container\Tests\Fixtures\Circular1');
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     * @expectedException \Solid\Container\NonInstantiableClassException
     */
    public function testNonInstantiableClass()
    {
        $this->container->resolve('Solid\Container\ContainerInterface');
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testRecursiveDependencyResolution()
    {
        $a = $this->container->resolve('Solid\Container\Tests\Fixtures\A');

        $this->assertInstanceOf(
            'Solid\Container\Tests\Fixtures\A',
            $a,
            'Should be able to recursively resolve dependencies'
        );
        $this->assertInstanceOf(
            'Solid\Container\Tests\Fixtures\B',
            $a->getDependency(),
            'Should be able to recursively resolve dependencies'
        );
        $this->assertInstanceOf(
            'Solid\Container\Tests\Fixtures\C',
            $a->getDependency()->getDependency(),
            'Should be able to recursively resolve dependencies'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testParentConstructorDependencyResolution()
    {
        $d = $this->container->resolve('Solid\Container\Tests\Fixtures\D');
        $e = $this->container->resolve('Solid\Container\Tests\Fixtures\E');

        $this->assertInstanceOf(
            'Solid\Container\Tests\Fixtures\B',
            $d->getDependency(),
            'Should be able to resolve parent constructor dependencies'
        );
        $this->assertInstanceOf(
            'Solid\Container\Tests\Fixtures\B',
            $e->getDependency(),
            'Should be able to resolve parent constructor dependencies'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testParameterDependencyResolution()
    {
        $a = $this->container->resolve('Solid\Container\Tests\Fixtures\A');
        $f = $this->container->resolve('Solid\Container\Tests\Fixtures\F', $a, 'test', 24);

        $this->assertEquals(
            [
                $a,
                'test',
                24
            ],
            $f->getDependencies(),
            'Should use passed parameters in dependency resolution'
        );
        $this->assertSame($a, $f->getDependencies()[0], 'Should use passed parameters in dependency resolution');

        $f = $this->container->resolve('Solid\Container\Tests\Fixtures\F', $a, 'test');
        $this->assertEquals(
            [
                $a,
                'test',
                0
            ],
            $f->getDependencies(),
            'Should use default parameters in dependency resolution'
        );

        try {
            $this->container->resolve('Solid\Container\Tests\Fixtures\F');

            $this->fail("Should throw a DependencyResolutionException when dependencies can't be resolve");
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Solid\Container\DependencyResolutionException',
                $exception,
                "Should throw a DependencyResolutionException when dependencies can't be resolve"
            );
        }
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testAlias()
    {
        $this->container->alias('Solid\Container\Tests\Fixtures\A', 'a');
        $a = $this->container->resolve('a');

        $this->assertInstanceOf('Solid\Container\Tests\Fixtures\A', $a, 'Should be able to alias abstracts');
        $this->assertTrue($this->container->isAlias('a'), 'Should be able to identify an alias');
        $this->assertFalse($this->container->isAlias('b'), 'Should be able to identify an alias');

        try {
            $this->container->alias('test', 'a');

            $this->fail('Should throw an AliasNotAvailableException when an alias is tried more than once');
        } catch (Exception $exception) {
            $this->assertInstanceOf(
                'Solid\Container\AliasNotAvailableException',
                $exception,
                'Should throw an AliasNotAvailableException when an alias is tried more than once'
            );
        }
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testBound()
    {
        $this->container->instance('test', 'Container test');
        $this->container->bind('stringFactory', [$this, 'stringFactory']);

        $this->assertTrue($this->container->isBound('test'), 'Should be able to identify bound abstracts');
        $this->assertTrue($this->container->isBound('stringFactory'), 'Should be able to identify bound abstracts');
        $this->assertFalse($this->container->isBound('unbound'), 'Should be able to identify bound abstracts');

        $this->container->alias('test', 'unbound');

        $this->assertTrue(
            $this->container->isBound('unbound'),
            'Should be able to identify bound abstracts via aliases'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testShared()
    {
        $this->container->bind('unsharedString', [$this, 'stringFactory']);
        $this->container->bind('sharedString', [$this, 'stringFactory'], true);
        $this->container->instance('sharedInstance', 'Container test');

        $this->assertFalse(
            $this->container->isShared('unsharedString'),
            'Should be able to tell if an abstract is shared'
        );
        $this->assertTrue(
            $this->container->isShared('sharedString'),
            'Should be able to tell if an abstract is shared'
        );
        $this->assertTrue(
            $this->container->isShared('sharedInstance'),
            'Should be able to tell if an abstract is shared'
        );
        $this->assertFalse(
            $this->container->isShared('unbound'),
            'Should not throw an exception if given abstract is unbound'
        );
    }

    /**
     * @api
     * @since 0.1.0
     * @return string
     */
    public function stringFactory(): string
    {
        ++$this->stringFactoryInvocations;

        return 'Container test';
    }

    /**
     * @api
     * @since 0.1.0
     * @param mixed ...$parameters Factory parameters.
     * @return array
     */
    public function parameterFactory(...$parameters): array
    {
        return $parameters;
    }
}
