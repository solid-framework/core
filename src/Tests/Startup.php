<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\App;

use Solid\Application;
use Solid\RunnableInterface;

/**
 * @package Solid\Tests
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class Startup implements RunnableInterface
{
    /**
     * @internal
     * @since 0.1.0
     * @var Application
     */
    protected $application;

    /**
     * @api
     * @since 0.1.0
     * @param Application $application An instance of the current application.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @api
     * @since 0.1.0
     * @return void
     */
    public function run()
    {
        $this->application->instance('testStartup', 'run');
    }
}
