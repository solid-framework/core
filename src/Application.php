<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid;

use Solid\Container\ResolveException;
use Solid\Kernel\Request as KernelRequest;

/**
 * @package Solid
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Application extends Container\Container implements ApplicationInterface
{
    /**
     * @internal
     * @since 0.1.0
     * @var string
     */
    protected $sapiNamespace;

    /**
     * @internal
     * @since 0.1.0
     * @var string
     */
    protected $directory;

    /**
     * @api
     * @since 0.1.0
     * @param string|null $directory     The application directory to use.
     * @param string|null $sapiNamespace The SAPI namespace to use.
     */
    public function __construct(string $directory = __DIR__ . '/App', $sapiNamespace = null)
    {
        $this->directory = $directory;
        $this->sapiNamespace = $sapiNamespace ?? (PHP_SAPI === 'cli' ? 'Cli' : 'Http');

        // bind the application
        $this->instance('Solid\Application', $this);
        $this->alias('Solid\Application', 'application');
        $this->alias('Solid\Application', 'app');

        // bind the configuration object
        $config = $this->resolve('Solid\Config\Config');
        $this->instance('config', $config);

        // load the application configuration
        if (is_readable($configFile = "{$this->directory}/config.json")) {
            $fileContents = file_get_contents($configFile);

            if (($appConfig = json_decode($fileContents, true)) !== null) {
                $config->set($appConfig);
            }
        }

        if (
            class_exists('Solid\App\Startup') &&
            in_array('Solid\RunnableInterface', (array) class_implements('Solid\App\Startup'))
        ) {
            $startup = $this->resolve('Solid\App\Startup');

            // start up the application
            $startup->run();
        }

        // bind the SAPI specific kernel
        $this->bind("Solid\\{$this->sapiNamespace}\\Kernel", null, true);
        $this->alias("Solid\\{$this->sapiNamespace}\\Kernel", 'kernel');
    }

    /**
     * @api
     * @since 0.1.0
     * @return void
     * @throws UnsupportedEnvironmentException
     */
    public function run()
    {
        try {
            $kernel = $this->resolve('kernel');
            $request = call_user_func(
                ["Solid\\{$this->sapiNamespace}\\Request", 'fromKernelRequest'],
                new KernelRequest
            );
        } catch (ResolveException $exception) {
            throw new UnsupportedEnvironmentException($exception->getMessage());
        }

        $kernel->dispatchResponse($kernel->handleRequest($request));
    }

    /**
     * @api
     * @since 0.1.0
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @api
     * @since 0.1.0
     * @return string
     */
    public function getSapiNamespace(): string
    {
        return $this->sapiNamespace;
    }
}
