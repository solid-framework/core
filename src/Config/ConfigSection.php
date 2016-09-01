<?php

/**
 * Copyright (c) 2016 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Solid\Config;

/**
 * @package Solid\Config
 * @author Martin Pettersson <martin@solid-framework.com>
 * @since 0.1.0
 */
class ConfigSection extends Config
{
    /**
     * @internal
     * @since 0.1.0
     * @var string
     */
    protected $prefix;

    /**
     * @internal
     * @since 0.1.0
     * @var Config
     */
    protected $config;

    /**
     * @api
     * @since 0.1.0
     * @param string $prefix A config section prefix.
     * @param Config $config A reference to the a config object.
     */
    public function __construct(string $prefix, Config &$config)
    {
        $this->prefix = trim($prefix, '.');
        $this->config = $config;

        if (!$this->config->has($this->prefix)) {
            $this->config->put($this->prefix, []);
        }
    }

    /**
     * @api
     * @since 0.1.0
     * @see Config::set
     * @param array $settings The settings to use.
     * @return void
     */
    public function set(array $settings)
    {
        $this->config->put($this->prefix, $settings);
    }

    /**
     * @api
     * @since 0.1.0
     * @see Config::has
     * @param string $settingsField The settings field to check.
     * @return bool
     */
    public function has(string $settingsField): bool
    {
        return $this->config->has("{$this->prefix}.{$settingsField}");
    }

    /**
     * @api
     * @since 0.1.0
     * @see Config::get
     * @param string $settingsField The settings field to retrieve.
     * @param mixed  $default       The default value to use.
     * @return mixed
     */
    public function get(string $settingsField = null, $default = null)
    {
        $settingsField = is_null($settingsField) ? $this->prefix : "{$this->prefix}.{$settingsField}";

        return $this->config->get($settingsField, $default);
    }

    /**
     * @api
     * @since 0.1.0
     * @see Config::put
     * @param string $settingsField The settings field to add/replace.
     * @param mixed  $value         The value to use.
     * @return void
     */
    public function put(string $settingsField, $value)
    {
        $settingsField = "{$this->prefix}.{$settingsField}";

        $this->config->put($settingsField, $value);
    }

    /**
     * @api
     * @since 0.1.0
     * @see Config::merge
     * @param array       $settings    The settings to merge into the given field.
     * @param string|null $field       The field to merge the given settings into.
     * @param bool        $mergeArrays Whether to merge indexed arrays.
     * @return void
     */
    public function merge(array $settings, string $field = null, bool $mergeArrays = false)
    {
        $settingsField = is_null($field) ? $this->prefix : "{$this->prefix}.{$field}";

        $this->config->merge($settings, $settingsField, $mergeArrays);
    }
}
