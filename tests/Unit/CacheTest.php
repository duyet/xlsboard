<?php

declare(strict_types=1);

namespace Xlsboard\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xlsboard\Cache;

class CacheTest extends TestCase
{
    private Cache $cache;
    private string $testCacheDir;

    protected function setUp(): void
    {
        $this->testCacheDir = sys_get_temp_dir() . '/xlsboard_test_cache_' . uniqid();
        $this->cache = new Cache($this->testCacheDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testCacheDir)) {
            $files = glob($this->testCacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testCacheDir);
        }
    }

    public function testSetAndGet(): void
    {
        $key = 'test_key';
        $value = ['foo' => 'bar'];

        $this->assertTrue($this->cache->set($key, $value, 60));
        $this->assertEquals($value, $this->cache->get($key));
    }

    public function testGetNonExistent(): void
    {
        $this->assertNull($this->cache->get('non_existent_key'));
    }

    public function testExpiration(): void
    {
        $key = 'expiring_key';
        $value = 'expiring_value';

        // Set with 1 second TTL
        $this->cache->set($key, $value, 1);

        // Should exist immediately
        $this->assertEquals($value, $this->cache->get($key));

        // Wait for expiration
        sleep(2);

        // Should be null after expiration
        $this->assertNull($this->cache->get($key));
    }

    public function testDelete(): void
    {
        $key = 'deletable_key';
        $value = 'deletable_value';

        $this->cache->set($key, $value);
        $this->assertEquals($value, $this->cache->get($key));

        $this->assertTrue($this->cache->delete($key));
        $this->assertNull($this->cache->get($key));
    }

    public function testClear(): void
    {
        $this->cache->set('key1', 'value1');
        $this->cache->set('key2', 'value2');
        $this->cache->set('key3', 'value3');

        $this->assertTrue($this->cache->clear());

        $this->assertNull($this->cache->get('key1'));
        $this->assertNull($this->cache->get('key2'));
        $this->assertNull($this->cache->get('key3'));
    }
}
