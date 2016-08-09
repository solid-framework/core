<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A dummy polyfill for getallheaders
 *
 * @api
 * @since 0.1.0
 * @return array
 */
function getallheaders(): array
{
    return ['test' => 'getallheaders'];
}
