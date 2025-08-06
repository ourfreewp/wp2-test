<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Actions;

class Action {
    protected $action_name;

    public function __construct(string $action_name) {
        $this->action_name = $action_name;
    }

    /**
     * Asserts that the action was added.
     * @return $this
     */
    public function is_added(): self {
        Actions\expectAdded($this->action_name)->once();
        return $this;
    }

    /**
     * Asserts that the action was done (fired).
     * @return $this
     */
    public function is_done(): self {
        Actions\expectDone($this->action_name)->once();
        return $this;
    }
}
