<?php
namespace WP2_Test\Unit;

use Brain\Monkey;

abstract class Scenario extends \PHPUnit\Framework\TestCase {

    protected $bench;
    protected $expectations; // For configuring simulators/mocks

    /**
     * Set up the test environment.
     * This method is called before each test method in the class.
     */

    protected function set_up(): void {
        parent::setUp();
        // Activate Brain Monkey
        Monkey\setUp();

        // Initialize Bench and Expectations
        $this->bench = new \WP2_Test\Core\Bench('unit');
        $this->expectations = new \WP2_Test\Core\Expectations();
    }

    /**
     * Tear down the test environment.
     * This method is called after each test method in the class.
     */
    protected function tear_down(): void {
        // Deactivate Brain Monkey
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Provides access to the Bench for creating test data/samples.
     * @return \WP2_Test\Core\Bench
     */
    protected function bench(): \WP2_Test\Core\Bench {
        return $this->bench;
    }

    /**
     * Provides access to the Expectations API for configuring simulators.
     * @return \WP2_Test\Core\Expectations
     */
    protected function expectations(): \WP2_Test\Core\Expectations {
        return $this->expectations;
    }

    /**
     * Configures the WP_Query Simulator for unit tests.
     * @param array $args The query arguments to match.
     * @return \WP2_Test\Simulators\Query
     */
    protected function mock_query_simulator(array $args): \WP2_Test\Simulators\Query {
        $simulator = new \WP2_Test\Simulators\Query($args);
        $this->expectations->add_simulator($simulator); // Register the simulator
        return $simulator;
    }

    // Add other common unit test helpers here
}
