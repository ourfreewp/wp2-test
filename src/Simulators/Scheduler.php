<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;

/**
 * Scheduler simulator for contract/service tests.
 */
class Scheduler
{
    protected static $queue = [];

    public static function boot()
    {
        // Replace scheduling functions with stubs
        Functions\when('wp_schedule_event')->alias(function($timestamp, $recurrence, $hook, $args = []) {
            self::$queue[] = compact('hook', 'args', 'timestamp', 'recurrence');
            return true;
        });
        Functions\when('as_schedule_single_action')->alias(function($timestamp, $hook, $args = [], $group = '') {
            self::$queue[] = compact('hook', 'args', 'timestamp', 'group');
            return true;
        });
    }

    public static function assert_scheduled($hook)
    {
        foreach (self::$queue as $job) {
            if ($job['hook'] === $hook) {
                return true;
            }
        }
        throw new \Exception("No scheduled job for hook: $hook");
    }

    public static function assert_scheduled_with_args($hook, $args)
    {
        foreach (self::$queue as $job) {
            if ($job['hook'] === $hook && $job['args'] == $args) {
                return true;
            }
        }
        throw new \Exception("No scheduled job for hook: $hook with args");
    }

    public static function tear_down()
    {
        self::$queue = [];
    }
}
