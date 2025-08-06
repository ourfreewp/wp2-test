<?php
namespace WP2_Test\Simulators;

use Brain\Monkey\Functions;

class Option {
    protected $option_name;

    public function __construct(string $option_name) {
        $this->option_name = $option_name;
    }

    /**
     * Specifies the value that the option should "return".
     * @param mixed $value The value to return when the option is queried.
     * @return $this
     */

    public function returns($value): self {
        Functions\when('get_option')
            ->justReturn($value)
            ->whenCalledWith([$this->option_name]);
        return $this;
    }

    /**
     * Specifies the value that the option should be updated to.
     * @param mixed $value The value to update the option to.
     * @return $this
     */

    public function updates_to($value): self {
        Functions\expect('update_option')
            ->once()
            ->with($this->option_name, $value)
            ->andReturn(true);
        return $this;
    }

    /**
     * Specifies that the option should be deleted.
     * @return $this
     */
    public function is_deleted(): self {
        Functions\expect('delete_option')
            ->once()
            ->with($this->option_name)
            ->andReturn(true);
        return $this;
    }

    // Add methods for other option-related assertions or behaviors (e.g., update, delete)
}
