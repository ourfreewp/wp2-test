<?php
namespace WP2_Test\Core;

use PHPUnit\Framework\Assert;

/**
 * Custom assertions for WordPress testing.
 */
trait Assertions
{
    public static function assert_is_wp_error($object, $message = '')
    {
        Assert::assertTrue(is_wp_error($object), $message ?: 'Failed asserting that object is a WP_Error.');
    }

    public static function assert_post_meta_equals($post_id, $key, $value, $message = '')
    {
        $actual = get_post_meta($post_id, $key, true);
        Assert::assertEquals($value, $actual, $message ?: "Failed asserting that post meta '$key' equals expected value.");
    }

    public static function assert_action_has_been_added($hook_name, $message = '')
    {
        global $wp_filter;
        $has = isset($wp_filter[$hook_name]) && !empty($wp_filter[$hook_name]);
        Assert::assertTrue($has, $message ?: "Failed asserting that action '$hook_name' has been added.");
    }
}
