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
- **`Data`**: A utility for generating fake data for your tests, powered by the popular Faker library. Ideal for creating realistic posts, users, and other data types.

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

-----

## Detailed Usage

### Generating Test Data

For more complex data needs, you can use the `Data` generator, which provides access to [Faker](https://github.com/FakerPHP/Faker) for creating realistic test data.

```php
<?php

use WP2_Test\Unit\Scenario;
use WP2_Test\Core\Generators\Data;

class MyDataTest extends Scenario
{
    public function test_with_generated_data()
    {
        $data_generator = new Data();
        $posts = $data_generator->create_posts(5);

        // Your test logic here
        $this->assertCount(5, $posts);
        $this->assertIsString($posts[0]['post_title']);
    }
}
```

### Simulator Examples

While unit tests should be isolated, you often need to test interactions with WordPress systems. The simulators provide a lightweight way to do this.

**Using the WPDB Simulator**

The `$wpdb` simulator runs on an in-memory SQLite database, allowing you to test database interactions without a real database connection. To use it, instantiate it in your test's `set_up` method and assign it to the global `$wpdb` variable.

```php
<?php
use WP2_Test\Unit\Scenario;
use WP2_Test\Simulators\WPDB;

class MyDbServiceTest extends Scenario
{
    protected function set_up(): void
    {
        parent::set_up();
        global $wpdb;
        $wpdb = new WPDB(); // Replace global $wpdb with our simulator
    }

    public function test_it_inserts_a_user()
    {
        global $wpdb;
        $result = $wpdb->insert('users', [
            'user_login' => 'test',
            'user_email' => 'test@example.com'
        ]);
        $this->assertTrue($result);

        $count = $wpdb->get_var("SELECT COUNT(*) FROM users WHERE user_login = 'test'");
        $this->assertEquals(1, $count);
    }
}
```
Testing Emails with the Mail Simulator

The Mail simulator intercepts calls to wp_mail() and stores emails in a "sentbox" for inspection.

```php
<?php
use WP2_Test\Unit\Scenario;
use WP2_Test\Simulators\Mail;

class MyNotificationTest extends Scenario
{
    protected function set_up(): void
    {
        parent::set_up();
        Mail::intercept(); // Start capturing emails
    }

    protected function tear_down(): void
    {
        Mail::tear_down(); // Clear sentbox
        parent::tear_down();
    }

    public function test_sends_welcome_email()
    {
        // Code that calls wp_mail('new@example.com', 'Welcome!', ...);
        send_welcome_email('new@example.com');

        Mail::assert_sent(1);
        Mail::assert_sent_to('new@example.com');
        Mail::assert_body_contains('Welcome');
    }
}
```
Testing Cron Events with the Scheduler Simulator

The Scheduler simulator intercepts calls to wp_schedule_event() and lets you assert that cron jobs were correctly scheduled.

```php
<?php
use WP2_Test\Unit\Scenario;
use WP2_Test\Simulators\Scheduler;

class MyCronTest extends Scenario
{
    protected function set_up(): void
    {
        parent::set_up();
        Scheduler::boot();
    }

    protected function tear_down(): void
    {
        Scheduler::tear_down();
        parent::tear_down();
    }

    public function test_schedules_daily_cleanup()
    {
        // Code that calls wp_schedule_event(..., 'my_cleanup_hook');
        schedule_my_tasks();

        Scheduler::assert_scheduled('my_cleanup_hook');
    }
}
```
**Simulating Users and Permissions**

The `User` simulator can mock the current user and their capabilities, which is essential for testing access control logic.

```php
<?php
use WP2_Test\Unit\Scenario;
use WP2_Test\Simulators\User;

class MyPermissionTest extends Scenario
{
    protected function tear_down(): void
    {
        User::reset(); // Clear user simulation
        parent::tear_down();
    }

    public function test_admin_can_access()
    {
        // Simulate an administrator
        $admin_user = (object) ['ID' => 1];
        User::set_current_user($admin_user);
        User::set_capability('manage_options', true);

        // Your code that calls current_user_can('manage_options')
        $this->assertTrue(current_user_can('manage_options'));
    }

    public function test_subscriber_cannot_access()
    {
        // Simulate a subscriber
        $subscriber = (object) ['ID' => 2];
        User::set_current_user($subscriber);
        User::set_capability('manage_options', false);

        $this->assertFalse(current_user_can('manage_options'));
    }
}
```
Using the Virtual Filesystem

The FS simulator, which uses vfsStream, allows you to test code that interacts with the filesystem without touching the actual disk.

```php
<?php
use WP2_Test\Unit\Scenario;
use WP2_Test\Simulators\FS;

class MyFileProcessorTest extends Scenario
{
    protected function set_up(): void
    {
        parent::set_up();
        FS::boot(); // Initialize the virtual filesystem
    }

    public function test_reads_config_from_file()
    {
        // Create a virtual file
        $config_path = '/configs/my-plugin.json';
        $json_content = '{"setting":"enabled"}';
        FS::create_file($config_path, $json_content);

        // Get the virtual path for the test
        $virtual_path = FS::get_vfs_path($config_path);

        // Code that calls file_get_contents($virtual_path)
        $config = json_decode(file_get_contents($virtual_path), true);

        $this->assertEquals('enabled', $config['setting']);
    }
}
```
