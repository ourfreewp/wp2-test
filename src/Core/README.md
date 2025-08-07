# Core

The Core directory contains the main framework logic for WP2 Test, including:

- **Assertions.php**: Custom assertions for WordPress testing (e.g., `assert_is_wp_error`).
- **Bench.php**: Fluent API for generating test data and samples for both unit and integration tests.
- **Expectations.php**: API for configuring simulators and mocks, used in unit tests.
- **Suites/**: Specialized assertion utilities for accessibility, i18n, and performance.
- **Generators/**: Data generators for creating realistic test data.
- **E2E/**: End-to-end test integration adapters and runners for external services.

## Usage Example

```php
use WP2_Test\Core\Bench;
$bench = new Bench('unit');
$user = $bench->user(['role' => 'editor'])->make();
```
