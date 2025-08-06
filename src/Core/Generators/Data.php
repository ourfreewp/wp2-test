<?php
namespace WP2_Test\Core\Generators;

use Faker\Factory as FakerFactory;

/**
 * Data generator for tests using Faker.
 */
class Data
{
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    public function create_posts($count = 1)
    {
        $posts = [];
        for ($i = 0; $i < $count; $i++) {
            $posts[] = [
                'post_title' => $this->faker->sentence,
                'post_content' => $this->faker->paragraph,
            ];
        }
        return $posts;
    }

    // Add more generators as needed
}
