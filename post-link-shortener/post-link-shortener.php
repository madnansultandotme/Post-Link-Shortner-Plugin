<?php
/**
 * Plugin Name: Post Link Shortener
 * Description: A plugin to shorten URLs in posts, track clicks, and display a feed.
 * Version: 1.0.0
 * Author: Muhammad Adnan Sultan
 * Author URI: https://example.com
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants
define( 'PLS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include files
include_once PLS_PLUGIN_DIR . 'includes/class-post-link-shortener-db.php';
include_once PLS_PLUGIN_DIR . 'includes/class-post-link-shortener.php';
include_once PLS_PLUGIN_DIR . 'includes/class-post-link-shortener-admin.php';
include_once PLS_PLUGIN_DIR . 'includes/class-post-link-shortener-public.php';

// Register activation hook
register_activation_hook( __FILE__, array( 'Post_Link_Shortener_DB', 'activate' ) );

// Initialize plugin
function pls_initialize_plugin() {
    $admin = new Post_Link_Shortener_Admin();
    $admin->run();

    $public = new Post_Link_Shortener_Public();
    $public->run();
}
add_action( 'plugins_loaded', 'pls_initialize_plugin' );

// Add rewrite rules on activation
function pls_add_rewrite_rules() {
    add_rewrite_rule('^([a-zA-Z0-9]{6})/?$', 'index.php?pls_short_url=$matches[1]', 'top');
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pls_add_rewrite_rules' );

// Remove rewrite rules on deactivation
function pls_remove_rewrite_rules() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pls_remove_rewrite_rules' );

// Add query var
function pls_add_query_vars($vars) {
    $vars[] = 'pls_short_url';
    return $vars;
}
add_filter('query_vars', 'pls_add_query_vars');

// Redirect based on query var
function pls_template_redirect() {
    global $wp_query;
    if (isset($wp_query->query_vars['pls_short_url'])) {
        $short_url = $wp_query->query_vars['pls_short_url'];
        $post_link_shortener_public = new Post_Link_Shortener_Public();
        $post_link_shortener_public->handle_redirect($short_url);
        exit;
    }
}
add_action('template_redirect', 'pls_template_redirect');
