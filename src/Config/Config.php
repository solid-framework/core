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
class Config
{
    /**
     * @internal
     * @since 0.1.0
     * @var array
     */
    protected $settings;

    /**
     * @api
     * @since 0.1.0
     * @param array|null $settings The settings object to use.
     */
    public function __construct(array $settings = [])
    {
        $this->set($settings);
    }

    /**
     * @api
     * @since 0.1.0
     * @param array $settings The settings to use.
     * @return void
     */
    public function set(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @api
     * @since 0.1.0
     * @param string $settingsField The settings field to check.
     * @return bool
     */
    public function has(string $settingsField): bool
    {
        $setting = $this->settings;

        foreach (explode('.', $settingsField) as $field) {
            if (is_array($setting) && array_key_exists($field, $setting)) {
                $setting = &$setting[$field];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the given settings field if set or the default value
     *
     * Note: If no settings field is given all settings are returned.
     *
     * @api
     * @since 0.1.0
     * @param string $settingsField The settings field to retrieve.
     * @param mixed  $default       The default value to use.
     * @return mixed
     */
    public function get(string $settingsField = null, $default = null)
    {
        // if no settings field was given return all settings
        if (is_null($settingsField)) {
            return $this->settings;
        }

        $setting = $this->settings;

        foreach (explode('.', $settingsField) as $field) {
            if (is_array($setting) && array_key_exists($field, $setting)) {
                $setting = &$setting[$field];
            } else {
                return $default;
            }
        }

        return $setting;
    }

    /**
     * Adds/replaces the given settings field
     *
     * @api
     * @since 0.1.0
     * @param string $settingsField The settings field to add/replace.
     * @param mixed  $value         The value to use.
     * @return void
     */
    public function put(string $settingsField, $value)
    {
        // create a reference to the settings to traverse
        $setting = &$this->settings;

        foreach (explode('.', $settingsField) as $field) {
            // primer the setting (override any previous value)
            if (!$this->isAssoc($setting)) {
                $setting = [];
            }

            // traverse the reference
            $setting = &$setting[$field];
        }

        // assign the referenced field
        $setting = $value;
    }

    /**
     * Merges the given settings into the given field
     *
     * @api
     * @since 0.1.0
     * @param array       $settings    The settings to merge into the given field.
     * @param string|null $field       The field to merge the given settings into.
     * @param bool       $mergeArrays Whether to merge indexed arrays.
     * @return void
     */
    public function merge(array $settings, string $field = null, bool $mergeArrays = false)
    {
        $merged = self::mergeSettings($this->get($field), $settings, $mergeArrays);

        if (!is_null($field)) {
            $this->put($field, $merged);
        } else {
            $this->set($merged);
        }
    }

    /**
     * Recursively merges the two given arrays giving priority to the second
     *
     * @internal
     * @since 0.1.0
     * @param array $a           The array to merge in the second array into.
     * @param array $b           The array to merge into the first array.
     * @param bool  $mergeArrays Whether to merge indexed arrays.
     * @return array
     */
    private static function mergeSettings(array $a, array $b, $mergeArrays = false): array
    {
        foreach ($b as $key => $value) {
            if (array_key_exists($key, $a)) {
                if (self::isAssoc($a[$key]) && self::isAssoc($b[$key])) {
                    $a[$key] = self::mergeSettings(
                        $a[$key],
                        $b[$key],
                        $mergeArrays
                    );
                } elseif ($mergeArrays && is_array($a[$key]) && is_array($b[$key])) {
                    $a[$key] = array_values(array_unique(array_merge($a[$key], $b[$key])));
                } else {
                    $a[$key] = $b[$key];
                }
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }

    /**
     * @internal
     * @since 0.1.0
     * @param mixed $array The value to check.
     * @return bool
     */
    private static function isAssoc($array): bool
    {
        return is_array($array) && count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
