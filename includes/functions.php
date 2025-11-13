<?php
if (! defined('ABSPATH')) exit;

// Helper functions in the global namespace.
function gamify_add_points($user_id, $points, $context, $args = [])
{
    return (new \Gamify\System\Points())->add($user_id, $points, $context, $args);
}

function gamify_deduct_points($user_id, $points, $context, $args = [])
{
    return (new \Gamify\System\Points())->deduct($user_id, $points, $context, $args);
}
