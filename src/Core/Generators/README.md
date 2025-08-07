# Core/Generators

Data generators for tests, using libraries like Faker to create sample posts, users, and other WordPress objects for testing purposes.

## Main Generator

- **Data.php**: Provides methods to generate posts and other objects for use in tests. Uses Faker for realistic data.

## Usage Example

```php
use WP2_Test\Core\Generators\Data;

$data = new Data();
$posts = $data->create_posts(5);
```

