<?php

declare(strict_types=1);

/**
 * Helper functions for xlsboard
 */

if (!function_exists('env')) {
    /**
     * Get environment variable with fallback
     *
     * @param string $key Variable name
     * @param mixed $default Default value
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get base path of application
     *
     * @param string $path Optional path to append
     * @return string
     */
    function base_path(string $path = ''): string
    {
        $base = __DIR__ . '/..';

        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get storage path
     *
     * @param string $path Optional path to append
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage/' . ltrim($path, '/'));
    }
}
