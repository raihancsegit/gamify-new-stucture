<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Manages all REST API endpoints for the plugin.
 */
class Gamify_API_Manager
{
    protected $namespace = 'gamify/v1';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register all API routes.
     */
    public function register_routes()
    {
        // Logs endpoint
        register_rest_route($this->namespace, '/logs', ['methods' => 'GET', 'callback' => [$this, 'get_logs'], 'permission_callback' => [$this, 'admin_permission_check']]);

        // Point Types endpoints
        register_rest_route($this->namespace, '/point-types', [
            ['methods' => 'GET', 'callback' => [$this, 'get_point_types'], 'permission_callback' => [$this, 'admin_permission_check']],
            ['methods' => 'POST', 'callback' => [$this, 'create_point_type'], 'permission_callback' => [$this, 'admin_permission_check']],
        ]);

        // Triggers endpoints
        register_rest_route($this->namespace, '/triggers', [
            ['methods' => 'GET', 'callback' => [$this, 'get_triggers'], 'permission_callback' => [$this, 'admin_permission_check']],
            ['methods' => 'POST', 'callback' => [$this, 'save_triggers'], 'permission_callback' => [$this, 'admin_permission_check']],
        ]);
    }

    /**
     * Permission check to ensure only administrators can access the endpoint.
     */
    public function admin_permission_check()
    {
        return current_user_can('manage_options');
    }

    public function get_logs($request)
    { /* ... Logic to get logs ... */
    }

    public function get_point_types($request)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'gamify_point_types';
        $results = $wpdb->get_results("SELECT id, name, plural_name, created_at as date FROM {$table} ORDER BY id DESC", ARRAY_A);
        return new WP_REST_Response(array_map(function ($row) {
            $row['key'] = $row['id'];
            return $row;
        }, $results), 200);
    }

    public function create_point_type($request)
    { /* ... Logic to create point type ... */
    }

    public function get_triggers($request)
    {
        $engine = new Gamify_Trigger_Engine();
        $available_triggers = $engine->get_available_triggers();
        global $wpdb;
        $active_triggers_raw = $wpdb->get_results("SELECT trigger_key, points_to_award FROM {$wpdb->prefix}gamify_triggers WHERE is_active = 1", OBJECT_K);
        $active_triggers = [];
        foreach ($active_triggers_raw as $key => $data) {
            $active_triggers[$key] = (int)$data->points_to_award;
        }
        return new WP_REST_Response(['available' => $available_triggers, 'active' => $active_triggers], 200);
    }

    public function save_triggers($request)
    {
        $active_hooks = $request->get_param('active_hooks');
        if (! is_array($active_hooks)) {
            return new WP_Error('invalid_data', 'Invalid data format.', ['status' => 400]);
        }
        global $wpdb;
        $table = $wpdb->prefix . 'gamify_triggers';
        $wpdb->query("UPDATE {$table} SET is_active = 0");
        foreach ($active_hooks as $key => $points) {
            $points = intval($points);
            if ($points == 0) continue;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE trigger_key = %s", $key));
            if ($exists) {
                $wpdb->update($table, ['points_to_award' => $points, 'is_active' => 1], ['trigger_key' => $key]);
            } else {
                $wpdb->insert($table, ['trigger_key' => $key, 'points_to_award' => $points, 'is_active' => 1]);
            }
        }
        return new WP_REST_Response(['message' => 'Settings saved successfully.'], 200);
    }
}
