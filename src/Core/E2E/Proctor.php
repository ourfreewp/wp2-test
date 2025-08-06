<?php
namespace WP2_Test\Core\E2E;

use WP2_Test\Core\E2E\Adapters\Integration;

/**
 * Proctor: E2E test runner, decoupled from provider.
 */
class Proctor
{
    /** @var Integration */
    protected static $integration;

    /**
     * Set the active E2E integration adapter.
     * @param Integration $adapter
     */
    public static function set_integration(Integration $adapter)
    {
        self::$integration = $adapter;
    }

    /**
     * Execute a suite using the configured adapter.
     * @param string $id
     * @param array $options
     * @return mixed
     */
    public static function execute_suite(string $id, array $options = [])
    {
        if (!self::$integration) {
            throw new \RuntimeException('No E2E integration adapter set.');
        }
        return self::$integration->execute_suite($id, $options);
    }

    /**
     * Get suite result using the configured adapter.
     * @param string $result_id
     * @return mixed
     */
    public static function get_suite_result(string $result_id)
    {
        if (!self::$integration) {
            throw new \RuntimeException('No E2E integration adapter set.');
        }
        return self::$integration->get_suite_result($result_id);
    }

    /**
     * Cancel suite run using the configured adapter.
     * @param string $result_id
     * @return mixed
     */
    public static function cancel_suite_run(string $result_id)
    {
        if (!self::$integration) {
            throw new \RuntimeException('No E2E integration adapter set.');
        }
        return self::$integration->cancel_suite_run($result_id);
    }
}
