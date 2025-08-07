<?php
// WP2 Test Framework Loader

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Use Composer's autoloader for all dependencies.
$autoloader = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

// Optionally, include init.php for custom bootstrapping.
$init = __DIR__ . '/src/init.php';
if ( file_exists( $init ) ) {
	require_once $init;
}
