<?php
namespace WP2_Test\Core\E2E\Adapters;

/**
 * Interface for E2E integration adapters.
 */
interface Integration
{
    /**
     * Execute a test suite by ID with options.
     * @param string $id
     * @param array $options
     * @return mixed
     */
    public function execute_suite(string $id, array $options);

    /**
     * Get the result of a suite run by result ID.
     * @param string $result_id
     * @return mixed
     */
    public function get_suite_result(string $result_id);

    /**
     * Cancel a suite run by result ID.
     * @param string $result_id
     * @return mixed
     */
    public function cancel_suite_run(string $result_id);
}
