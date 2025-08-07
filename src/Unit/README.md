# Unit

Unit test base classes and helpers for WP2 Test. Use these for fast, isolated tests that do not require a WordPress environment.

- **Scenario.php**: Base class for unit tests, extends PHPUnit\Framework\TestCase and activates Brain Monkey. Provides access to Bench and Expectations for creating test data and configuring simulators.

## Usage Example

```php
use WP2_Test\Unit\Scenario;

class MyUnitTest extends Scenario {
    public function test_mock_option() {
        $this->expectations()->option('my_option')->returns('value');
        $this->assertEquals('value', get_option('my_option'));
    }
}
```
