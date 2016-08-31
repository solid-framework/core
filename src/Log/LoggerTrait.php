<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Log;

use Solid\Log\Formatters\FormatterInterface;

/**
 * @package Solid\Log
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
trait LoggerTrait
{
    /**
     * @internal
     * @since 0.1.0
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @internal
     * @since 0.1.0
     * @param string $message The message to interpolate.
     * @param array  $context The context to interpolate the message with.
     * @return string
     */
    protected function interpolate(string $message, array $context = []): string
    {
        $replacements = [];

        foreach ($context as $key => $value) {
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replacements["{{$key}}"] = $value;
            }
        }

        return strtr($message, $replacements);
    }

    /**
     * @api
     * @since 0.1.0
     * @param FormatterInterface $formatter The formatter to use.
     * @return void
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @api
     * @since 0.1.0
     * @return FormatterInterface|null
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
