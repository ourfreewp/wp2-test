<?php
namespace WP2_Test\Simulators;

/**
 * In-memory WP Object Cache simulator for contract/service tests.
 */
class Cache
{
    protected static $cache = [];

    public static function wp_cache_get($key)
    {
        return self::$cache[$key] ?? false;
    }

    public static function wp_cache_set($key, $value)
    {
        self::$cache[$key] = $value;
        return true;
    }

    public static function wp_cache_delete($key)
    {
        unset(self::$cache[$key]);
        return true;
    }

    public static function wp_cache_flush()
    {
        self::$cache = [];
        return true;
    }

    public static function assert_cache_hit($key)
    {
        if (!array_key_exists($key, self::$cache)) {
            throw new \Exception("Cache miss for key: $key");
        }
        return true;
    }

    public static function assert_cache_miss($key)
    {
        if (array_key_exists($key, self::$cache)) {
            throw new \Exception("Cache hit for key: $key");
        }
        return true;
    }
}
