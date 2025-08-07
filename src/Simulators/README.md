# Simulators

This directory contains in-memory simulators for WordPress internals, enabling fast contract/service tests without booting WordPress or requiring a database. Each simulator provides a mock implementation of a WordPress subsystem, allowing you to assert behaviors and configure test scenarios with minimal overhead.

## Available Simulators

- **Action.php**: Simulates WordPress actions. Assert that actions are added or fired using `is_added()` and `is_done()`.
- **Filter.php**: Simulates WordPress filters. Assert that filters are added or applied using `is_added()` and `is_applied()`.
- **User.php**: Simulates user authentication, roles, and capabilities. Set the current user and capabilities for tests.
- **Cache.php**: In-memory object cache simulator. Use to test caching logic and assert cache hits/misses.
- **HTTP.php**: Intercepts and mocks outbound HTTP requests. Configure responses for specific URLs and methods.
- **Mail.php**: Fake mailbox for asserting email delivery. Intercept and assert sent emails, recipients, and message bodies.
- **Option.php**: Simulates WordPress options API (`get_option`, `update_option`, `delete_option`). Configure expected values and updates.
- **Query.php**: Mocks `WP_Query` and `get_posts`. Configure which posts should be returned for specific queries.
- **REST.php**: Simulates registration of REST API routes. Assert that routes are registered as expected.
- **Scheduler.php**: Mocks WordPress cron and action scheduler functions. Assert scheduled jobs and their arguments.
- **WPDB.php**: In-memory SQLite-based simulator for `$wpdb`. Use for contract tests that require database-like operations.
- **FS.php**: Virtual filesystem using vfsStream. Create files and directories for tests without touching the real filesystem.

## Usage Example

Most simulators are used via the `Expectations` API in your unit tests. For example:

```php
$this->expectations()->option('my_option')->returns('value');
$this->expectations()->action('init')->is_added();
```

See each file for more details and available methods.
