<?php
namespace WP2_Test\Admin\Console;

/**
 * Handles the creation of the Visual Test Runner admin page.
 */
class Core {

	/**
	 * Hooks into WordPress to add the admin menu and AJAX actions.
	 */
	public function boot() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'wp_ajax_wp2_run_tests', [ $this, 'run_tests_ajax_handler' ] );
	}

	/**
	 * Executes the PHPUnit test suite and returns structured output.
	 */
	public function run_tests_ajax_handler() {
		if ( ! current_user_can( 'manage_options' ) )
			wp_send_json_error( 'Permission denied.', 403 );
		check_ajax_referer( 'wp2_test_runner_nonce', 'nonce' );

		$project_root = dirname( __DIR__, 3 );
		$phpunit_path = $project_root . '/vendor/bin/phpunit';
		$junit_log_path = sys_get_temp_dir() . '/wp2-junit-' . uniqid() . '.xml';

		if ( ! is_executable( $phpunit_path ) ) {
			wp_send_json_error( 'Error: PHPUnit executable not found at ' . $phpunit_path );
		}

		$command = sprintf( '%s --log-junit %s 2>&1', $phpunit_path, escapeshellarg( $junit_log_path ) );
		$raw_output = shell_exec( $command );

		if ( ! file_exists( $junit_log_path ) ) {
			wp_send_json_error( [ 'output' => $raw_output ?: 'PHPUnit command produced no output. Check your phpunit.xml configuration.' ] );
		}

		$xml = simplexml_load_file( $junit_log_path );
		$summary = [];
		$failures = [];

		foreach ( $xml->testsuite as $suite ) {
			$summary['tests'] = ( $summary['tests'] ?? 0 ) + (int) $suite['tests'];
			$summary['assertions'] = ( $summary['assertions'] ?? 0 ) + (int) $suite['assertions'];
			$summary['failures'] = ( $summary['failures'] ?? 0 ) + (int) $suite['failures'];
			$summary['errors'] = ( $summary['errors'] ?? 0 ) + (int) $suite['errors'];

			if ( $suite->testcase ) {
				foreach ( $suite->testcase as $testcase ) {
					if ( $testcase->failure || $testcase->error ) {
						$failures[] = (string) ( $testcase->failure ?? $testcase->error );
					}
				}
			}
		}

		@unlink( $junit_log_path );

		wp_send_json_success( [ 'raw_output' => $raw_output, 'summary' => $summary, 'failures' => $failures ] );
	}

	/**
	 * Adds the top-level menu page to the WordPress admin.
	 */
	public function add_admin_menu() {
		add_menu_page( 'WP2 Test Runner', 'Test Runner', 'manage_options', 'wp2-test-runner', [ $this, 'render_page' ], 'dashicons-hammer', 3 );
	}

	/**
	 * Renders the HTML and inline JavaScript for the admin page.
	 */
	public function render_page() {
		?>
		<div class="wrap" id="wp2-test-runner-app">
			<h1><?php echo esc_html__( 'WP2 Visual Test Runner', 'wp2-test' ); ?></h1>
			<div id="test-summary" class="notice" style="display:none;"></div>

			<div class="test-runner-controls">
				<button id="run-tests-btn" class="button button-primary">Run All Tests</button>
			</div>

			<div class="test-runner-output">
				<h2>Results</h2>
				<pre id="results-pane" class="results-pane">Click "Run All Tests" to begin.</pre>
			</div>

			<style>
				.test-runner-output {
					margin-top: 2rem;
				}

				.results-pane {
					background-color: #23282d;
					color: #e0e0e0;
					border: 1px solid #ccc;
					padding: 1rem;
					white-space: pre-wrap;
					word-wrap: break-word;
					min-height: 200px;
					max-height: 600px;
					overflow-y: auto;
				}

				.results-pane.running {
					opacity: 0.7;
				}
			</style>

			<script>
				document.addEventListener('DOMContentLoaded', function () {
					const runButton = document.getElementById('run-tests-btn');
					const resultsPane = document.getElementById('results-pane');
					const summaryBox = document.getElementById('test-summary');

					runButton.addEventListener('click', function () {
						resultsPane.textContent = 'Running...';
						summaryBox.style.display = 'none';
						runButton.disabled = true;

						fetch(ajaxurl, {
							method: 'POST',
							body: new URLSearchParams({
								'action': 'wp2_run_tests',
								'nonce': '<?php echo wp_create_nonce( 'wp2_test_runner_nonce' ); ?>'
							})
						})
							.then(response => response.json())
							.then(result => {
								if (result.success) {
									const { raw_output, summary, failures } = result.data;
									resultsPane.textContent = raw_output;

									let summary_html = '';
									const total_failures = (summary.failures || 0) + (summary.errors || 0);

									if (total_failures > 0) {
										summary_html = `<p><strong>❌ Tests failed.</strong></p>`;
										summary_html += `<p>${summary.tests} tests, ${summary.assertions} assertions, ${total_failures} failures.</p>`;
										summaryBox.className = 'notice notice-error';
									} else {
										summary_html = `<p><strong>✅ All tests passed!</strong></p>`;
										summary_html += `<p>${summary.tests} tests, ${summary.assertions} assertions.</p>`;
										summaryBox.className = 'notice notice-success';
									}
									summaryBox.innerHTML = summary_html;
									summaryBox.style.display = 'block';

								} else {
									resultsPane.textContent = result.data.output || ('AJAX Error: ' + (result.data || 'Unknown error'));
									summaryBox.innerHTML = `<p><strong>An error occurred while running tests.</strong></p>`;
									summaryBox.className = 'notice notice-error';
									summaryBox.style.display = 'block';
								}
							})
							.catch(error => { resultsPane.textContent = 'Fetch Error: ' + error.message; })
							.finally(() => { runButton.disabled = false; });
					});
				});
			</script>
		</div>
		<?php
	}
}
