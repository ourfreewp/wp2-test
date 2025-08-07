<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;
use PHPUnit\Framework\Assert;

/**
 * In-memory WP Object Cache simulator for unit tests.
 * This simulator intercepts calls to wp_cache_* functions.
 */
class Cache
{
    protected static $cache = [];

    /**
     * Boots the simulator, redirecting cache functions to this class.
     */
    public static function boot()
    {
        Functions\when('wp_cache_get')->alias([__CLASS__, 'get']);
        Functions\when('wp_cache_set')->alias([__CLASS__, 'set']);
        Functions\when('wp_cache_add')->alias([__CLASS__, 'add']);
        Functions\when('wp_cache_delete')->alias([__CLASS__, 'delete']);
        Functions\when('wp_cache_flush')->alias([__CLASS__, 'flush']);
    }

    /**
     * Tears down the simulator, clearing the cache.
     */
    public static function tear_down()
    {
        self::$cache = [];
    }

    public static function get($key, $group = '', $force = false, &$found = null)
    {
        $found = isset(self::$cache[$group][$key]);
        return self::$cache[$group][$key] ?? false;
    }

    public static function set($key, $data, $group = '', $expire = 0)
    {
        self::$cache[$group][$key] = $data;
        return true;
    }

    public static function add($key, $data, $group = '', $expire = 0)
    {
        if (isset(self::$cache[$group][$key])) {
            return false;
        }
        self::$cache[$group][$key] = $data;
        return true;
    }

    public static function delete($key, $group = '')
    {
        if (isset(self::$cache[$group][$key])) {
            unset(self::$cache[$group][$key]);
            return true;
        }
        return false;
    }

    public static function flush()
    {
        self::$cache = [];
        return true;
    }

    public static function assert_hit($key, $group = '')
    {
        Assert::assertTrue(isset(self::$cache[$group][$key]), "Failed asserting that cache has key '$key' in group '$group'.");
    }

    public static function assert_miss($key, $group = '')
    {
        Assert::assertFalse(isset(self::$cache[$group][$key]), "Failed asserting that cache does not have key '$key' in group '$group'.");
    }
}
