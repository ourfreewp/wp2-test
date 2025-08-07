<?php
namespace WP2_Test\CLI;

if ( ! class_exists( 'WP_CLI_Command' ) ) {
	return;
}
/**
 * The main container for all WP2 Test commands.
 */
class Test extends \WP_CLI_Command {
	// This class acts as a simple container and does not need an __invoke method.
}
