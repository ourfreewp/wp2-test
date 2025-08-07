<?php
namespace WP2_Test\CLI;

if ( ! class_exists( 'WP_CLI_Command' ) ) {
	return;
}

/**
 * Scaffolds test files for classes.
 */
class Scaffold_Test extends \WP_CLI_Command {
	/**
	 * Creates a new test file for a given class.
	 *
	 * ## OPTIONS
	 *
	 * <class_name>
	 * : The fully qualified name of the class to create a test for.
	 *
	 * [--type=<type>]
	 * : Force a specific test type.
	 * ---
	 * default: auto
	 * options:
	 * - auto
	 * - unit
	 * - integration
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * # Automatically detect and create the best test type
	 * wp test scaffold "MyPlugin\Core\Service"
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $class_name ) = $args;

		if ( ! class_exists( $class_name ) ) {
			\WP_CLI::error( "Class '$class_name' does not exist or could not be found." );
			return;
		}

		$reflector = new \ReflectionClass( $class_name );

		$test_type = $assoc_args['type'] === 'auto'
			? $this->analyze_class_dependencies( $reflector )
			: ucfirst( $assoc_args['type'] );

		\WP_CLI::line( "Analysis complete. Generating a '$test_type' test..." );

		$content = $this->generate_test_content( $reflector, $test_type );
		$file_path = $this->write_test_file( $reflector, $test_type, $content );

		if ( $file_path ) {
			\WP_CLI::success( "Test file created: $file_path" );
		}
	}

	private function write_test_file( \ReflectionClass $reflector, string $test_type, string $content ) {
		$source_path = $reflector->getFileName();
		$project_root = dirname( dirname( $source_path ) );
		$class_namespace = $reflector->getNamespaceName();
		$relative_path = str_replace( '\\', '/', $class_namespace );
		$test_dir = sprintf(
			'%s/tests/%s/%s',
			$project_root,
			strtolower( $test_type ),
			$relative_path
		);
		if ( ! is_dir( $test_dir ) ) {
			if ( ! mkdir( $test_dir, 0755, true ) ) {
				\WP_CLI::error( "Could not create directory: $test_dir" );
				return false;
			}
		}
		$test_file_path = $test_dir . '/' . $reflector->getShortName() . 'Test.php';
		if ( file_exists( $test_file_path ) ) {
			\WP_CLI::warning( "Test file already exists, skipping: $test_file_path" );
			return false;
		}
		if ( ! file_put_contents( $test_file_path, $content ) ) {
			\WP_CLI::error( "Could not write to file: $test_file_path" );
			return false;
		}
		return $test_file_path;
	}

	private function generate_test_content( \ReflectionClass $reflector, string $test_type ): string {
		$class_namespace = $reflector->getNamespaceName();
		$short_name = $reflector->getShortName();
		$full_class_name = $reflector->getName();
		$test_namespace = 'Tests\\' . $test_type . '\\' . $class_namespace;
		$test_class_name = $short_name . 'Test';
		$base_class = '\WP2_Test\\' . $test_type . '\\Scenario';
		$setup_content = 'parent::set_up();';
		if ( $test_type === 'Unit' ) {
			$mock_lines = $this->generate_mock_suggestions( $reflector );
			if ( ! empty( $mock_lines ) ) {
				$setup_content .= "\n\n        // Auto-generated mock suggestions:\n";
				$setup_content .= "        $this->expectations()\n";
				$setup_content .= implode( "\n", $mock_lines );
			}
		}
		return <<<PHP
<?php
namespace {$test_namespace};

use {$base_class};
use {$full_class_name};

/**
 * Test case for {$short_name}.
 *
 * @covers \\{$full_class_name}
 */
class {$test_class_name} extends Scenario {

    public function set_up(): void {
        {$setup_content}
    }

    public function tear_down(): void {
        // Your teardown logic here.
        parent::tear_down();
    }

    /**
     * @test
     */
    public function it_should_be_instantiable() {
        $this->assertInstanceOf(
            {$short_name}::class,
            new {$short_name}()
        );
    }
}
PHP;
	}

	private function generate_mock_suggestions( \ReflectionClass $reflector ): array {
		$file_content = file_get_contents( $reflector->getFileName() );
		$tokens = token_get_all( $file_content );
		$suggestions = [];
		$watch_functions = [ 
			'get_option' => 'option',
			'add_action' => 'action',
			'apply_filters' => 'filter',
		];
		foreach ( $tokens as $index => $token ) {
			if ( ! is_array( $token ) || $token[0] !== T_STRING || ! isset( $watch_functions[ $token[1] ] ) ) {
				continue;
			}
			$next_token_index = $index + 1;
			while ( isset( $tokens[ $next_token_index ] ) && is_array( $tokens[ $next_token_index ] ) && $tokens[ $next_token_index ][0] === T_WHITESPACE ) {
				$next_token_index++;
			}
			if ( isset( $tokens[ $next_token_index ] ) && $tokens[ $next_token_index ] === '(' ) {
				$arg_token_index = $next_token_index + 1;
				while ( isset( $tokens[ $arg_token_index ] ) && is_array( $tokens[ $arg_token_index ] ) && $tokens[ $arg_token_index ][0] === T_WHITESPACE ) {
					$arg_token_index++;
				}
				if ( isset( $tokens[ $arg_token_index ] ) && is_array( $tokens[ $arg_token_index ] ) && $tokens[ $arg_token_index ][0] === T_CONSTANT_ENCAPSED_STRING ) {
					$arg_value = trim( $tokens[ $arg_token_index ][1], "'\"" );
					$mock_type = $watch_functions[ $token[1] ];
					$method = ( $mock_type === 'option' ) ? '->returns(false);' : '->is_added();';
					$suggestions[ $mock_type . $arg_value ] = "            ->{$mock_type}('{$arg_value}'){$method}";
				}
			}
		}
		return array_values( $suggestions );
	}

	private function analyze_class_dependencies( \ReflectionClass $reflector ): string {
		try {
			$file_path = $reflector->getFileName();
			if ( ! $file_path || ! file_exists( $file_path ) )
				return 'Unit';
			$content = file_get_contents( $file_path );
			$integration_keywords = [ '$wpdb', 'wp_remote_post', 'wp_remote_get', 'wp_remote_request' ];
			foreach ( $integration_keywords as $keyword ) {
				if ( str_contains( $content, $keyword ) ) {
					\WP_CLI::log( "Detected usage of '$keyword', recommending Integration test." );
					return 'Integration';
				}
			}
		} catch (\ReflectionException $e) {
			\WP_CLI::warning( "Could not reflect on class: " . $e->getMessage() );
			return 'Unit';
		}
		\WP_CLI::log( 'No heavy dependencies found, recommending Unit test.' );
		return 'Unit';
	}
}
