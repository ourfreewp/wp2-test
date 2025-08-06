<?php
namespace WP2_Test\Core\E2E;

class Client {
    private const API_BASE = 'https://api.ghostinspector.com/v1/';
    private $api_key;

    public function __construct(string $api_key) {
        if (empty($api_key)) {
            throw new \InvalidArgumentException('Ghost Inspector API key is required.');
        }
        $this->api_key = $api_key;
    }

    /**
     * Executes a test suite on Ghost Inspector.
     * @param string $suite_id The ID of the suite to execute.
     * @return object The API response.
     */
    public function execute_suite(string $suite_id): object {
        $url = self::API_BASE . "suites/{$suite_id}/execute/?apiKey={$this->api_key}";
        $response = wp_remote_post($url);

        if (is_wp_error($response)) {
            throw new \Exception('Failed to connect to Ghost Inspector: ' . $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if ($body->code !== 'SUCCESS') {
            throw new \Exception('Ghost Inspector API Error: ' . $body->error);
        }

        return $body->data;
    }
}
