# Core/E2E

End-to-end (E2E) testing components for WP2 Test. These allow you to run tests against external services and real environments.

## Structure

- **Adapters/**: Integration adapters for external E2E services (e.g., Ghost Inspector).
- **Client.php**: API client for Ghost Inspector, used to trigger and retrieve suite executions.
- **Proctor.php**: E2E test runner, decoupled from provider. Set the adapter to run tests via different services.

## Usage Example

Set up an adapter and run a suite:

```php
use WP2_Test\Core\E2E\Proctor;
use WP2_Test\Core\E2E\Adapters\GhostInspector;

Proctor::set_integration(new GhostInspector($api_key));
$result = Proctor::execute_suite($suite_id, $options);
```
