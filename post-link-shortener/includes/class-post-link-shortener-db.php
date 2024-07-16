<?php

class Post_Link_Shortener_DB {

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pls_url_mappings';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            original_url text NOT NULL,
            short_url varchar(6) NOT NULL,
            click_count int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY short_url (short_url)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
