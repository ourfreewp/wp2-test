# Core/Suites

Specialized assertion utilities for WP2 Test. These suites provide helpers for advanced testing scenarios:

- **A11y.php**: Accessibility assertions using axe-core. Run accessibility checks on HTML output.
- **I18n.php**: Internationalization helpers and assertions. Stub and assert translation lookups.
- **Perf.php**: Performance assertions for time and memory budgets. Benchmark code execution and assert resource usage.

## Usage Example

```php
use WP2_Test\Core\A11y;
A11y::assert_html_is_accessible($html);
```
