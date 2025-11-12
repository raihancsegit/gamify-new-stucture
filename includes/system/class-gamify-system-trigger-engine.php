<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * The main engine that listens to WordPress hooks and triggers point awards.
 */
class Gamify_System_Trigger_Engine
{
    private $available_triggers = [];

    public function __construct()
    {
        $this->define_triggers();
        $this->attach_hooks();
    }

    /**
     * Define all available triggers for the system.
     */
    private function define_triggers()
    {
        $this->available_triggers = [
            'wp_login' => ['label' => 'User logs in', 'hook' => 'wp_login', 'args' => 2, 'get_user_id' => function ($user_login, $user) {
                return $user->ID;
            }],
            'publish_post' => ['label' => 'User publishes a new post', 'hook' => 'publish_post', 'args' => 2, 'get_user_id' => function ($post_id, $post) {
                return $post->post_author;
            }],
            'comment_post' => ['label' => 'User posts a comment', 'hook' => 'comment_post', 'args' => 1, 'get_user_id' => function ($comment_id) {
                $comment = get_comment($comment_id);
                return $comment ? $comment->user_id : 0;
            }],
        ];
    }

    /**
     * Dynamically attach all defined hooks to WordPress.
     */
    private function attach_hooks()
    {
        foreach ($this->available_triggers as $key => $trigger_data) {
            add_action($trigger_data['hook'], function () use ($key, $trigger_data) {
                $this->execute_trigger($key, $trigger_data, func_get_args());
            }, 10, $trigger_data['args']);
        }
    }

    /**
     * The main execution function when a hook is fired.
     */
    public function execute_trigger($key, $trigger_data, $hook_args)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'gamify_triggers';
        $rule = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE trigger_key = %s AND is_active = 1", $key));

        if (!$rule || (int)$rule->points_to_award === 0) {
            return;
        }

        $user_id = call_user_func_array($trigger_data['get_user_id'], $hook_args);
        if (!$user_id) {
            return;
        }

        $points = (int) $rule->points_to_award;
        $description = !empty($rule->log_description) ? $rule->log_description : $trigger_data['label'];
        $args = ['description' => $description];

        if ($points > 0) {
            gamify_add_points($user_id, $points, $key, $args);
        } elseif ($points < 0) {
            gamify_deduct_points($user_id, abs($points), $key, $args);
        }
    }

    /**
     * Public method to get all defined triggers for the API.
     */
    public function get_available_triggers()
    {
        return $this->available_triggers;
    }
}
