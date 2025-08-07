<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;

/**
 * HTTP simulator for outbound HTTP requests.
 */
class HTTP {
	protected static $mocks = [];

	public static function boot() {
		Functions\when( 'apply_filters' )->alias( function ($tag, $value, ...$args) {
			if ( $tag === 'pre_http_request' ) {
				$request_args = $args[0] ?? [];
				$url = $args[1] ?? '';
				foreach ( self::$mocks as $mock ) {
					// A more robust match would check the method from $request_args['method']
					if ( $mock['url'] === $url ) {
						return [ 
							'response' => [ 
								'code' => $mock['response_code'],
								'message' => 'OK'
							],
							'body' => $mock['body'],
							'headers' => [],
							'cookies' => [],
							'filename' => null,
						];
					}
				}
				return false; // Let the request proceed if no mock matches
			}
			return \apply_filters( $tag, $value, ...$args ); // Pass through other filters
		} );
	}

	public static function mock( $method, $url, $response ) {
		self::$mocks[] = [ 
			'method' => strtoupper( $method ),
			'url' => $url,
			'response_code' => $response['response'] ?? 200,
			'body' => $response['body'] ?? '',
		];
	}

	public static function tear_down() {
		self::$mocks = [];
	}
}
