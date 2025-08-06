<?php
// WP2 Test Framework Loader
// (Stub plugin loader for mu-plugins)

// Autoload all classes in src/ using PSR-4 style
spl_autoload_register(function ($class) {
    $prefix = 'WP2_Test\\';
    $base_dir = __DIR__ . '/src/';
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Optionally, include init.php for custom bootstrapping
$init = __DIR__ . '/src/init.php';
if (file_exists($init)) {
    require_once $init;
}
