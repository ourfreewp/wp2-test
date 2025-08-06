<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;

/**
 * User simulator for authentication, roles, and capabilities.
 */
class User
{
    protected static $current_user = null;
    protected static $capabilities = [];

    public static function set_current_user($user)
    {
        self::$current_user = $user;
        Functions\when('wp_get_current_user')->alias(function() use ($user) {
            return $user;
        });
        Functions\when('get_current_user_id')->alias(function() use ($user) {
            return $user ? ($user->ID ?? 0) : 0;
        });
    }

    public static function set_capability($cap, $allowed)
    {
        self::$capabilities[$cap] = $allowed;
        Functions\when('current_user_can')->alias(function($capability) {
            return self::$capabilities[$capability] ?? false;
        });
    }

    public static function reset()
    {
        self::$current_user = null;
        self::$capabilities = [];
    }
}
