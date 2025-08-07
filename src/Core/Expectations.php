<?php
namespace WP2_Test\Core;

class Expectations {
	protected $simulators = [];

	public function add_simulator( $simulator ): void {
		$this->simulators[] = $simulator;
	}

	/**
	 * Configures a mock for WordPress options.
	 * @param string $option_name The name of the option to mock.
	 * @return \WP2_Test\Simulators\Option
	 */
	public function option( string $option_name ): \WP2_Test\Simulators\Option {
		$simulator = new \WP2_Test\Simulators\Option( $option_name );
		$this->add_simulator( $simulator );
		return $simulator;
	}

	/**
	 * Configures a mock for WordPress actions.
	 * @param string $action_name The name of the action to mock.
	 * @return \WP2_Test\Simulators\Action
	 */
	public function action( string $action_name ): \WP2_Test\Simulators\Action {
		$simulator = new \WP2_Test\Simulators\Action( $action_name );
		$this->add_simulator( $simulator );
		return $simulator;
	}

	/**
	 * Configures a mock for WordPress filters.
	 * @param string $filter_name The name of the filter to mock.
	 * @return \WP2_Test\Simulators\Filter
	 */
	public function filter( string $filter_name ): \WP2_Test\Simulators\Filter {
		$simulator = new \WP2_Test\Simulators\Filter( $filter_name );
		$this->add_simulator( $simulator );
		return $simulator;
	}

	/**
	 * Configures a mock for WordPress REST API endpoints.
	 * @param string $route The REST route to mock.
	 * @return \WP2_Test\Simulators\REST
	 */
	public function rest_api( string $route ): \WP2_Test\Simulators\REST {
		$simulator = new \WP2_Test\Simulators\REST( $route );
		$this->add_simulator( $simulator );
		return $simulator;
	}
}
