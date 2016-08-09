<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Kernel\Tests;

use ReflectionClass;
use Solid\Kernel\Request;
use PHPUnit_Framework_TestCase;

/**
 * @package Solid\Kernel\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testConstructor()
    {
        // $_SESSION needs special treatment as it isn't always available
        unset($GLOBALS['_SESSION']);

        $request = new Request;

        $this->assertEquals($GLOBALS, $request->getGlobalParameters(), 'Request should use default globals');
        $this->assertEquals($_SERVER, $request->getServerParameters(), 'Request should use default server parameters');
        $this->assertEquals($_GET, $request->getGetParameters(), 'Request should use default get parameters');
        $this->assertEquals($_POST, $request->getPostParameters(), 'Request should use default post parameters');
        $this->assertEquals($_FILES, $request->getFileParameters(), 'Request should use default files parameters');
        $this->assertEquals($_COOKIE, $request->getCookieParameters(), 'Request should use default cookie parameters');
        $this->assertEquals([], $request->getSessionParameters(), 'Request should fallback to empty session parameters');
        $this->assertEquals($_REQUEST, $request->getRequestParameters(), 'Request should use default request parameters');
        $this->assertEquals($_ENV, $request->getEnvParameters(), 'Request should use default env parameters');

        // getallheaders needs special treatment as it isn't always available
        if (function_exists('getallheaders')) {
            $this->assertEquals(getallheaders(), $request->getHeaderParameters(), 'Request should use default header parameters');
        } else {
            require_once __DIR__ . '/Fixtures/getallheaders.php';

            $headerRequest = new Request;
            $this->assertEquals(['test' => 'getallheaders'], $headerRequest->getHeaderParameters(), 'Request should fallback to header parameters from the server global');
        }

        // test $_SESSION explicitly
        $GLOBALS['_SESSION'] = ['test' => 'session'];
        $sessionRequest = new Request;
        $this->assertEquals($_SESSION, $sessionRequest->getSessionParameters(), 'Request should use default session parameters');

        $request = new Request(
            ['globals' => 'override'],
            ['server' => 'override'],
            ['get' => 'override'],
            ['post' => 'override'],
            ['files' => 'override'],
            ['cookie' => 'override'],
            ['session' => 'override'],
            ['request' => 'override'],
            ['env' => 'override'],
            ['getallheaders' => 'override']
        );

        $this->assertEquals(['globals' => 'override'], $request->getGlobalParameters(), 'Request should override given globals');
        $this->assertEquals(['server' => 'override'], $request->getServerParameters(), 'Request should override given server parameters');
        $this->assertEquals(['get' => 'override'], $request->getGetParameters(), 'Request should override given get parameters');
        $this->assertEquals(['post' => 'override'], $request->getPostParameters(), 'Request should override given post parameters');
        $this->assertEquals(['files' => 'override'], $request->getFileParameters(), 'Request should override given files parameters');
        $this->assertEquals(['cookie' => 'override'], $request->getCookieParameters(), 'Request should override given cookie parameters');
        $this->assertEquals(['session' => 'override'], $request->getSessionParameters(), 'Request should override given session parameters');
        $this->assertEquals(['request' => 'override'], $request->getRequestParameters(), 'Request should override given request parameters');
        $this->assertEquals(['env' => 'override'], $request->getEnvParameters(), 'Request should override given env parameters');
        $this->assertEquals(['getallheaders' => 'override'], $request->getHeaderParameters(), 'Request should override given header parameters');
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testGetAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            require_once __DIR__ . 'Fixtures/getallheaders.php';
        }

        $request = new Request;

        $this->assertEquals(getallheaders(), $request->getHeaderParameters(), 'Should use "getallheaders" if available');
    }

    /**
     * @api
     * @since 0.1.0
     * @test
     */
    public function testGetAllHeadersPolyfill()
    {
        // make method accessible
        $reflection = new ReflectionClass('Solid\Kernel\Request');
        $getAllHeadersPolyfill = $reflection->getMethod('getAllHeadersPolyfill');
        $getAllHeadersPolyfill->setAccessible(true);

        $request = new Request(null, []);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals([], $headers);

        $request = new Request(null, ['TEST' => 'Test value']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals([], $headers);

        $request = new Request(null, ['HTTP_TEST' => 'Test value']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Test' => 'Test value'], $headers);

        $request = new Request(null, ['CONTENT_TYPE' => 'Test value']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Content-Type' => 'Test value'], $headers);

        $request = new Request(null, ['REDIRECT_HTTP_AUTHORIZATION' => 'Test authorization']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Authorization' => 'Test authorization'], $headers);

        $request = new Request(null, ['PHP_AUTH_DIGEST' => 'Test authorization']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Authorization' => 'Test authorization'], $headers);

        $request = new Request(null, ['PHP_AUTH_USER' => 'username']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Authorization' => 'Basic ' . base64_encode('username:')], $headers);

        $request = new Request(null, ['PHP_AUTH_USER' => 'username', 'PHP_AUTH_PW' => 'password']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Authorization' => 'Basic ' . base64_encode('username:password')], $headers);

        $request = new Request(null, ['PHP_AUTH_USER' => 'username', 'PHP_AUTH_PW' => 'password']);
        $headers = $getAllHeadersPolyfill->invoke($request);
        $this->assertEquals(['Authorization' => 'Basic ' . base64_encode('username:password')], $headers);
    }
}
