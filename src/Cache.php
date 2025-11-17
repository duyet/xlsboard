<?php

declare(strict_types=1);

namespace Xlsboard;

/**
 * Simple file-based cache
 */
class Cache
{
    private string $cacheDir;

    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? __DIR__ . '/../cache';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Get cached value
     *
     * @param string $key Cache key
     * @return mixed|null Returns null if not found or expired
     */
    public function get(string $key)
    {
        $file = $this->getCacheFile($key);

        if (!file_exists($file)) {
            return null;
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            return null;
        }

        $data = unserialize($contents);

        // Check expiration.
        if ($data['expires'] < time()) {
            $this->delete($key);

            return null;
        }

        return $data['value'];
    }

    /**
     * Set cache value
     *
     * @param string $key Cache key
     * @param mixed $value Value to cache
     * @param int $ttl Time to live in seconds
     * @return bool
     */
    public function set(string $key, $value, int $ttl = 300): bool
    {
        $file = $this->getCacheFile($key);

        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    /**
     * Delete cached value
     *
     * @param string $key Cache key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $file = $this->getCacheFile($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    /**
     * Clear all cache
     *
     * @return bool
     */
    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*.cache');

        if ($files === false) {
            return false;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Get cache file path
     *
     * @param string $key Cache key
     * @return string
     */
    private function getCacheFile(string $key): string
    {
        $hash = md5($key);

        return $this->cacheDir . '/' . $hash . '.cache';
    }
}
