# WP2 Test

WP2 Test is the unified, container-free testing framework for the WP2 ecosystem. It is designed to help developers write fast, reliable, and expressive tests for WordPress plugins and themes, minimizing the overhead of the traditional testing pyramid by removing the need to boot WordPress or manage a database connection for most tests.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Unit Tests](#unit-tests)
  - [Integration Tests](#integration-tests)
- [Architecture Overview](#architecture-overview)
  - [Core Components](#core-components)
  - [Simulators](#simulators)
- [Contributing](#contributing)
- [License](#license)
- [Credits & Acknowledgements](#credits--acknowledgements)

-----

## Installation

To get started, you'll need to have [Composer](https://getcomposer.org/) installed.

1. **Require the package:**

    ```bash
    composer require --dev wp2/wp2-test
    ```

2. **Install dependencies:**

    ```bash
    composer install
    ```

This will install the framework along with its dependencies, including **PHPUnit**, **Mockery**, and **Brain Monkey**.

-----

## Configuration

For certain integrations, like the Ghost Inspector E2E tests, you will need to configure environment variables. You can do this by creating a `.env` file in your project root.

| Variable                  | Description                                       | Default Value |
| ------------------------- | ------------------------------------------------- | ------------- |
| `GHOST_INSPECTOR_API_KEY` | Your API key for the Ghost Inspector service.     | `null`        |

-----

## Usage

The framework is designed to be used with PHPUnit. You can run tests by pointing to your test suites.

### Unit Tests

Unit tests are designed to be fast and isolated. They use simulators to mock WordPress functionality. Your unit test classes should extend `WP2_Test\Unit\Scenario`.

To run the unit test suite:

```bash
./vendor/bin/phpunit --testsuite unit
```

Here's a basic example of a unit test:

```php
<?php

use WP2_Test\Unit\Scenario;

class MyPluginTest extends Scenario
{
    public function test_something()
    {
        // Use expectations to mock WordPress functions
        $this->expectations()
             ->option('my_plugin_setting')
             ->returns('some_value');

        // Your test logic here
        $this->assertEquals('some_value', get_option('my_plugin_setting'));
    }
}
```

### Integration Tests

Integration tests run against a real, temporary WordPress database, providing a more realistic testing environment. Your integration test classes should extend `WP2_Test\Integration\Scenario`.

To run the integration test suite:

```bash
./vendor/bin/phpunit --testsuite integration
```

-----

## Architecture Overview

WP2 Test is architected as a "must-use" WordPress plugin (`wordpress-muplugin`). Its primary goal is to provide a comprehensive testing toolkit that covers everything from unit to end-to-end (E2E) tests.

The framework's power comes from its extensive use of **Simulators**, which are in-memory mock implementations of WordPress's core systems. This allows for rapid execution of unit and contract tests without the overhead of a database or a full WordPress instance.

### Core Components

- **`Bench`**: A data factory for creating test objects like users, posts, and more, for both unit and integration tests.
- **`Assertions`**: A trait with custom PHPUnit assertions tailored for WordPress, such as `assert_is_wp_error()` and `assert_post_meta_equals()`.
- **`Expectations`**: A fluent API for setting up mock behaviors for simulators in unit tests.
- **`Proctor`**: An E2E test runner that can be configured with different adapters, such as the built-in `GhostInspector` adapter.
- **Specialized Suites**: Includes helpers and assertions for:
  - **`A11y`**: Accessibility testing using `axe-core`.
  - **`I18n`**: Internationalization testing.
  - **`Perf`**: Performance benchmark testing.

### Simulators

The framework provides a wide array of simulators for core WordPress functionalities:

- **`Action` & `Filter`**: Mocks the WordPress hook system.
- **`Cache`**: An in-memory object cache simulator.
- **`FS`**: A virtual filesystem using vfsStream.
- **`HTTP`**: Intercepts and mocks outbound HTTP requests.
- **`Mail`**: A fake mailbox to assert that emails are sent correctly.
- **`Option`**: Simulates `get_option`, `update_option`, etc.
- **`Query`**: Mocks `WP_Query` and `get_posts`.
- **`REST`**: Simulates the registration of REST API routes.
- **`Scheduler`**: Mocks `wp_schedule_event` and other cron functions.
- **`User`**: Simulates user authentication and capabilities.
- **`WPDB`**: An in-memory SQLite-based simulator for `$wpdb`.

-----

## Contributing

At this time, there is no formal `CONTRIBUTING.md` file. However, contributions are welcome. Please open an issue or submit a pull request.

-----

## License

The WP2 Test framework is open-source software licensed under the **MIT License**.

-----

## Credits & Acknowledgements

- **Nils Adermann** and **Jordi Boggiano** (for Composer)
- **Sebastian Bergmann** (for PHPUnit)
- **Pádraic Brady** (for Mockery)
- **Giuseppe Mazzapica** (for Brain Monkey)
