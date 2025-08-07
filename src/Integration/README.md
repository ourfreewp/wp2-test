# Integration

Integration test base classes and helpers for WP2 Test. Use these for tests that require a real WordPress environment and database.

- **Scenario.php**: Base class for integration tests, extends WP_UnitTestCase. Provides helpers for setting up and tearing down the test environment, and for creating real database entries via Bench.

## Usage Example

```php
use WP2_Test\Integration\Scenario;

class MyIntegrationTest extends Scenario {
    public function test_real_db() {
        $user = $this->bench()->user(['role' => 'author'])->make();
        $this->assertNotEmpty($user->ID);
    }
}
```
