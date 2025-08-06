<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;

/**
 * HTTP simulator for outbound HTTP requests.
 */
class HTTP
{
    protected static $mocks = [];

    public static function boot()
    {
        Functions\when('apply_filters')->alias(function($tag, $value, ...$args) {
            if ($tag === 'pre_http_request') {
                $method = $args[0] ?? 'GET';
                $url = $args[1] ?? '';
                foreach (self::$mocks as $mock) {
                    if ($mock['method'] === $method && $mock['url'] === $url) {
                        return [$mock['response'], $mock['body']];
                    }
                }
                return false;
            }
            return $value;
        });
    }

    public static function mock($method, $url, $response)
    {
        self::$mocks[] = [
            'method' => strtoupper($method),
            'url' => $url,
            'response' => $response['response'] ?? 200,
            'body' => $response['body'] ?? '',
        ];
    }

    public static function tear_down()
    {
        self::$mocks = [];
    }
}
