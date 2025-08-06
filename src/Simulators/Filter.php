<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Filters;

class Filter {
    protected $filter_name;

    public function __construct(string $filter_name) {
        $this->filter_name = $filter_name;
    }

    /**
     * Asserts that the filter was added.
     * @return $this
     */
    public function is_added(): self {
        Filters\expectAdded($this->filter_name)->once();
        return $this;
    }

    /**
     * Asserts that the filter was applied.
     * @return $this
     */
    public function is_applied(): self {
        Filters\expectApplied($this->filter_name)->once();
        return $this;
    }
}
