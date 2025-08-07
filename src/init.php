<?php
// WP2 Test Framework custom initialization (stub)
// Add any custom bootstrapping here if needed.

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WP_CLI::add_command( 'wp2 test', 'WP2_Test\CLI\Test' );
	\WP_CLI::add_command( 'wp2 test scaffold', 'WP2_Test\CLI\Scaffold_Test' );
	\WP_CLI::add_command( 'wp2 test generate-stubs', 'WP2_Test\CLI\Generate_Stubs' );
}

if ( is_admin() ) {
	$admin_page = new \WP2_Test\Admin\Console\Core();
	$admin_page->boot();
}
