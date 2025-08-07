<?php
namespace WP2_Test\Core\Suites;

use PHPUnit\Framework\AssertionFailedError;

/**
 * Accessibility assertion utility using axe-core.
 */
class A11y {
	/**
	 * Assert that the given HTML is accessible according to axe-core.
	 *
	 * @param string $html
	 * @throws AssertionFailedError
	 */
	public static function assert_html_is_accessible( string $html ) {
		$tmp = tempnam( sys_get_temp_dir(), 'a11y_' ) . '.html';
		file_put_contents( $tmp, $html );
		$cmd = "npx axe-core-cli '$tmp' --exit 0 --reporter json";
		$output = shell_exec( $cmd );
		@unlink( $tmp );
		if ( ! $output ) {
			throw new AssertionFailedError( 'axe-core did not return output. Is Node.js/axe-core-cli installed?' );
		}
		$result = json_decode( $output, true );
		if ( ! empty( $result['violations'] ) ) {
			$messages = array_map( function ($v) {
				return $v['help'] . ': ' . $v['description'];
			}, $result['violations'] );
			throw new AssertionFailedError( "Accessibility violations found:\n" . implode( "\n", $messages ) );
		}
		// Passes if no violations
		return true;
	}
}
