<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;

/**
 * Mail simulator for contract/service tests.
 */
class Mail {
	protected static $sentbox = [];

	public static function intercept() {
		Functions\when( 'wp_mail' )->alias( function ($to, $subject, $message, $headers = '', $attachments = []) {
			self::$sentbox[] = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
			return true; // Simulate a successful send
		} );
	}

	public static function assert_sent( $count ) {
		if ( count( self::$sentbox ) !== $count ) {
			throw new \Exception( "Expected $count emails, got " . count( self::$sentbox ) );
		}
		return true;
	}

	public static function assert_sent_to( $address ) {
		foreach ( self::$sentbox as $mail ) {
			if ( $mail['to'] === $address || ( is_array( $mail['to'] ) && in_array( $address, $mail['to'] ) ) ) {
				return true;
			}
		}
		throw new \Exception( "No email sent to $address" );
	}

	public static function assert_body_contains( $string ) {
		foreach ( self::$sentbox as $mail ) {
			if ( strpos( $mail['message'], $string ) !== false ) {
				return true;
			}
		}
		throw new \Exception( "No email body contains: $string" );
	}

	public static function tear_down() {
		self::$sentbox = [];
	}
}
