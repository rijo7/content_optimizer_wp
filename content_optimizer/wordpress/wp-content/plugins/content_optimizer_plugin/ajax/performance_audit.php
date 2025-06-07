<?php
if (!defined('ABSPATH')) exit;

// Register AJAX handler
add_action('wp_ajax_run_performance_audit', 'run_performance_audit_callback');

function run_performance_audit_callback()
{
    // 1. Check if 'url' was sent
    if (!isset($_POST['url']) || empty($_POST['url'])) {
        wp_send_json_error(['error' => 'URL is required']);
        return;
    }

    $url = esc_url_raw($_POST['url']);

    // 2. Make the request to the audit server
    $response = wp_remote_post('https://lighthouse-audit-server.onrender.com', [
        'body'    => json_encode(['url' => $url]),
        'headers' => ['Content-Type' => 'application/json'],
        'timeout' => 30,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['error' => $response->get_error_message()]);
        return;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    // 3. Check if performance score is present
    if (isset($body['performance'])) {
        $performance_score = intval($body['performance']);

        // 4. Save performance score to database
        global $wpdb;
        $table = $wpdb->prefix . 'optimizer_performance_history';
        $wpdb->insert($table, [
            'url'   => $url,
            'score' => $performance_score,
        ]);

        // 5. Send success response
        wp_send_json_success([
            'performance' => $performance_score
        ]);
    } else {
        wp_send_json_error(['error' => 'Invalid response from audit server']);
    }
}

// AJAX for fetching history
add_action('wp_ajax_get_performance_history', 'get_performance_history');

function get_performance_history()
{
    if (!isset($_POST['url']) || empty($_POST['url'])) {
        wp_send_json_error(['error' => 'URL is required']);
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'optimizer_performance_history';
    $url = esc_url_raw($_POST['url']);

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT score, checked_at FROM $table WHERE url = %s ORDER BY checked_at ASC", $url),
        ARRAY_A
    );

    wp_send_json_success($results);
}
