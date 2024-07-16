<?php
// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove the custom database table.
global $wpdb;
$table_name = $wpdb->prefix . 'pls_url_mappings';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// Remove the plugin options.
delete_option( 'pls_custom_domain' );
