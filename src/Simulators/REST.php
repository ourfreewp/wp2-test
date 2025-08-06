<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Actions;

class REST {
    protected $route;

    public function __construct(string $route) {
        $this->route = $route;
    }

    /**
     * Asserts that the REST route was registered.
     * @return $this
     */
    public function is_registered(): self {
        Actions\expectAdded('rest_api_init')->once();
        return $this;
    }
}
