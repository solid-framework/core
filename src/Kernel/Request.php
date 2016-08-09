<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Kernel;

/**
 * @package Solid\Kernel
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Request
{
    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $globalParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $serverParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $getParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $postParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $fileParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $cookieParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $sessionParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $requestParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $envParameters;

    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $headerParameters;

    /**
     * @api
     * @since 0.1.0
     * @param array|null $globalParameters  The parameters to override the global parameters.
     * @param array|null $serverParameters  The parameters to override the server parameters.
     * @param array|null $getParameters     The parameters to override the get parameters.
     * @param array|null $postParameters    The parameters to override the post parameters.
     * @param array|null $fileParameters    The parameters to override the file parameters.
     * @param array|null $cookieParameters  The parameters to override the cookie parameters.
     * @param array|null $sessionParameters The parameters to override the session parameters.
     * @param array|null $requestParameters The parameters to override the request parameters.
     * @param array|null $envParameters     The parameters to override the env parameters.
     * @param array|null $headerParameters  The parameters to override the header parameters.
     */
    public function __construct(
        array $globalParameters = null,
        array $serverParameters = null,
        array $getParameters = null,
        array $postParameters = null,
        array $fileParameters = null,
        array $cookieParameters = null,
        array $sessionParameters = null,
        array $requestParameters = null,
        array $envParameters = null,
        array $headerParameters = null
    ) {
        $this->globalParameters = $globalParameters ?? $GLOBALS;
        $this->serverParameters = $serverParameters ?? $_SERVER;
        $this->getParameters = $getParameters ?? $_GET;
        $this->postParameters = $postParameters ?? $_POST;
        $this->fileParameters = $fileParameters ?? $_FILES;
        $this->cookieParameters = $cookieParameters ?? $_COOKIE;
        $this->sessionParameters = $sessionParameters ?? $_SESSION ?? [];
        $this->requestParameters = $requestParameters ?? $_REQUEST;
        $this->envParameters = $envParameters ?? $_ENV;
        $this->headerParameters = $headerParameters ?? $this->getAllHeaders();
    }

    /**
     * @internal
     * @since 0.1.0
     * @return array
     */
    protected function getAllHeaders(): array
    {
        return function_exists('getallheaders') ? getallheaders() : $this->getAllHeadersPolyfill();
    }

    /**
     * @internal
     * @since 0.1.0
     * @return array
     */
    protected function getAllHeadersPolyfill(): array
    {
        $headers = [];
        $copyServer = [
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5'
        ];

        foreach ($this->serverParameters as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);

                if (!array_key_exists($key, $copyServer) || !array_key_exists($key, $this->serverParameters)) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (array_key_exists($key, $copyServer)) {
                $headers[$copyServer[$key]] = $value;
            }
        }

        if (!array_key_exists('Authorization', $headers)) {
            if (array_key_exists('REDIRECT_HTTP_AUTHORIZATION', $this->serverParameters)) {
                $headers['Authorization'] = $this->serverParameters['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (array_key_exists('PHP_AUTH_USER', $this->serverParameters)) {
                $headers['Authorization'] = 'Basic ' . base64_encode(
                        $this->serverParameters['PHP_AUTH_USER'] . ':' . ($this->serverParameters['PHP_AUTH_PW'] ?? '')
                    );
            } elseif (array_key_exists('PHP_AUTH_DIGEST', $this->serverParameters)) {
                $headers['Authorization'] = $this->serverParameters['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getGlobalParameters(): array
    {
        return $this->globalParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getServerParameters(): array
    {
        return $this->serverParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getGetParameters(): array
    {
        return $this->getParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getPostParameters(): array
    {
        return $this->postParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getFileParameters(): array
    {
        return $this->fileParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getCookieParameters(): array
    {
        return $this->cookieParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getSessionParameters(): array
    {
        return $this->sessionParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getEnvParameters(): array
    {
        return $this->envParameters;
    }

    /**
     * @api
     * @since 0.1.0
     * @return array
     */
    public function getHeaderParameters(): array
    {
        return $this->headerParameters;
    }
}
