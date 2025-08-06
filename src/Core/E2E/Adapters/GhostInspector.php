<?php
namespace WP2_Test\Core\E2E\Adapters;

use WP2_Test\Core\E2E\Adapters\Integration;

/**
 * Ghost Inspector E2E adapter.
 */
class GhostInspector implements Integration
{
    protected $api_key;

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
    }

    public function execute_suite(string $id, array $options)
    {
        // Example: POST to Ghost Inspector API to start suite
        $endpoint = "https://api.ghostinspector.com/v1/suites/{$id}/execute/?apiKey={$this->api_key}";
        $args = [
            'method' => 'POST',
            'body' => $options,
        ];
        return wp_remote_request($endpoint, $args);
    }

    public function get_suite_result(string $result_id)
    {
        $endpoint = "https://api.ghostinspector.com/v1/executions/{$result_id}/?apiKey={$this->api_key}";
        return wp_remote_get($endpoint);
    }

    public function cancel_suite_run(string $result_id)
    {
        // Ghost Inspector does not support canceling executions via API as of 2024
        return false;
    }
}
