<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Manages all point-related database operations.
 */
class Gamify_System_Points_Manager
{
    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'gamify_points';
    }

    public function add_points($user_id, $points_to_add, $context, $args = [])
    {
        if (! is_numeric($user_id) || $user_id <= 0 || ! is_numeric($points_to_add) || $points_to_add <= 0) {
            return false;
        }
        global $wpdb;
        $data = [
            'user_id' => absint($user_id),
            'points' => absint($points_to_add),
            'context' => sanitize_key($context),
            'reference_id' => isset($args['reference_id']) ? absint($args['reference_id']) : null,
            'description' => isset($args['description']) ? sanitize_textarea_field($args['description']) : '',
        ];
        $result = $wpdb->insert($this->table_name, $data);
        if (! $result) {
            return false;
        }
        $insert_id = $wpdb->insert_id;
        do_action('gamify_points_added', $user_id, $points_to_add, $context, $insert_id);
        return $insert_id;
    }

    public function deduct_points($user_id, $points_to_deduct, $context, $args = [])
    {
        if (! is_numeric($user_id) || $user_id <= 0 || ! is_numeric($points_to_deduct) || $points_to_deduct <= 0) {
            return false;
        }
        global $wpdb;
        $data = [
            'user_id' => absint($user_id),
            'points' => -absint($points_to_deduct),
            'context' => sanitize_key($context),
            'reference_id' => isset($args['reference_id']) ? absint($args['reference_id']) : null,
            'description' => isset($args['description']) ? sanitize_textarea_field($args['description']) : '',
        ];
        $result = $wpdb->insert($this->table_name, $data);
        if (! $result) {
            return false;
        }
        $insert_id = $wpdb->insert_id;
        do_action('gamify_points_deducted', $user_id, $points_to_deduct, $context, $insert_id);
        return $insert_id;
    }

    public function get_user_total_points($user_id)
    {
        if (! is_numeric($user_id) || $user_id <= 0) {
            return 0;
        }
        global $wpdb;
        $total = $wpdb->get_var($wpdb->prepare("SELECT SUM(points) FROM {$this->table_name} WHERE user_id = %d", absint($user_id)));
        return (int) $total;
    }
}
