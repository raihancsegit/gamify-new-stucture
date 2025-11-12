<?php
if (! defined('ABSPATH')) {
    exit;
}

function gamify_add_points($user_id, $points, $context, $args = [])
{
    return (new Gamify_System_Points_Manager())->add_points($user_id, $points, $context, $args);
}

function gamify_deduct_points($user_id, $points, $context, $args = [])
{
    return (new Gamify_System_Points_Manager())->deduct_points($user_id, $points, $context, $args);
}

function gamify_get_user_total_points($user_id)
{
    return (new Gamify_System_Points_Manager())->get_user_total_points($user_id);
}
