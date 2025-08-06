<?php
namespace WP2_Test\Integration;


// This class now extends the official WordPress test case
abstract class Scenario extends \WP_UnitTestCase {

    protected $bench;

    /**
     * Set up the test environment for integration tests.
     * This would typically involve setting up a dedicated test database.
     */

    public function set_up(): void {
        parent::set_up(); // This handles the DB transaction setup
        $this->bench = new \WP2_Test\Core\Bench('integration');
    }

    /**
     * Tear down the test environment for integration tests.
     * This would typically involve cleaning up the test database.
     */

    public function tear_down(): void {
        parent::tear_down(); // This handles the DB transaction rollback
    }

    /**
     * Provides access to the Bench for creating real database entries.
     * @return \WP2_Test\Core\Bench
     */
    protected function bench(): \WP2_Test\Core\Bench {
        return $this->bench;
    }

    // Add other common integration test helpers here
}
