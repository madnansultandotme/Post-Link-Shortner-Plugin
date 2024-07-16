<?php
/**
 * Plugin Name: Post Link Shortener
 * Description: A plugin to shorten URLs in posts, track clicks, and display a feed.
 * Version: 1.0.0
 * Author: Muhammad Adnan Sultan
 * Author URI: https://github.com/madnansultandotme/Post-Link-Shortner-Plugin.git
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
