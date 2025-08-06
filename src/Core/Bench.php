<?php
namespace WP2_Test\Core;


class Bench {
    protected $context = 'unit'; // 'unit' or 'integration'
    protected $current_type;
    protected $current_properties = [];

    public function __construct(string $context = 'unit') {
        $this->context = $context;
    }

    /**
     * Starts the fluent chain for creating a user sample.
     * @param array $properties Initial properties for the user.
     * @return $this
     */
    public function user(array $properties = []): self {
        $this->current_type = 'user';
        $this->current_properties = $properties;
        return $this;
    }

    /**
     * Starts the fluent chain for creating a post sample.
     * @param array $properties Initial properties for the post.
     * @return $this
     */
    public function post(array $properties = []): self {
        $this->current_type = 'post';
        $this->current_properties = $properties;
        return $this;
    }

    /**
     * Adds an author to the current sample (e.g., post).
     * @param object $user_sample The user object (Sample) to set as author.
     * @return $this
     */
    public function with_author(object $user_sample): self {
        if ($this->current_type === 'post') {
            $this->current_properties['post_author'] = $user_sample->ID ?? null; // Assuming user has an ID
        }
        return $this;
    }

    /**
     * Loads a named, reusable recipe (template) for the current sample type.
     * @param string $prep_name The name of the predefined prep.
     * @return $this
     */
    public function using_prep(string $prep_name): self {
        // Conceptual: Load predefined properties for a 'prep'
        // In a real system, this would load from a registry of 'preps'.
        $preps = [
            'default_blog_post' => ['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Default Blog Post'],
            // ... other preps
        ];

        if (isset($preps[$prep_name])) {
            $this->current_properties = array_merge($this->current_properties, $preps[$prep_name]);
        }
        return $this;
    }

    /**
     * Creates the concrete object or data (Sample) based on the fluent chain.
     * @return object The created sample object.
     */
    public function make(): object {
        if ($this->context === 'integration') {
            // Integration: Call real WordPress functions
            return $this->make_integration_sample();
        }
        // Default to Unit: Return a mock object
        return $this->make_unit_sample();
    }

    private function make_unit_sample(): object {
        $sample = (object) $this->current_properties;
        $sample->ID = $sample->ID ?? mt_rand(1000, 9999); // Assign a dummy ID
        // Reset for next fluent chain
        $this->current_type = null;
        $this->current_properties = [];
        return $sample;
    }

    private function make_integration_sample(): object {
        $properties = $this->current_properties;
        $id = 0;

        if ($this->current_type === 'user') {
            if (!isset($properties['user_pass'])) $properties['user_pass'] = 'password';
            if (!isset($properties['user_login'])) $properties['user_login'] = 'testuser_' . time();
            $id = wp_insert_user($properties);
        } elseif ($this->current_type === 'post') {
            $id = wp_insert_post($properties);
        }

        if (is_wp_error($id)) {
            throw new \Exception("Failed to create integration sample: " . $id->get_error_message());
        }

        $type = $this->current_type;
        // Reset for next fluent chain
        $this->current_type = null;
        $this->current_properties = [];

        // Return the full WP_User or WP_Post object
        return ($type === 'user') ? get_user_by('ID', $id) : get_post($id);
    }
}
