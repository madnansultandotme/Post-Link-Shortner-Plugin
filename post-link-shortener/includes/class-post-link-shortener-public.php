<?php

class Post_Link_Shortener_Public {

    public function run() {
        add_action( 'save_post', array( $this, 'shorten_urls_in_post' ), 10, 3 );
        add_action( 'template_redirect', array( $this, 'handle_redirect' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
    }

    public function shorten_urls_in_post($post_id, $post, $update) {
        // Skip revisions and autosaves
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }

        // Check if it's a post
        if ( 'post' !== $post->post_type ) {
            return;
        }

        // Get post content
        $content = $post->post_content;

        // Extract and shorten URLs
        $shortenedContent = $this->extract_and_shorten_urls($content);

        // Update post content
        remove_action( 'save_post', array( $this, 'shorten_urls_in_post' ), 10 );
        wp_update_post( array(
            'ID' => $post_id,
            'post_content' => $shortenedContent
        ));
        add_action( 'save_post', array( $this, 'shorten_urls_in_post' ), 10, 3 );
    }

    private function extract_and_shorten_urls($content) {
        $pattern = '/https?:\/\/[^\s]+/';
        return preg_replace_callback($pattern, function($matches) {
            $shortUrl = $this->get_short_url($matches[0]);
            $customDomain = get_option('pls_custom_domain');
            return $customDomain ? $customDomain . "/$shortUrl" : home_url('/') . "$shortUrl";
        }, $content);
    }

    private function get_short_url($originalUrl) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pls_url_mappings';
        $shortUrl = '';
        do {
            $shortUrl = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE short_url = %s", $shortUrl));
        } while ($exists);
        $wpdb->insert($table_name, array('original_url' => $originalUrl, 'short_url' => $shortUrl, 'click_count' => 0));
        return $shortUrl;
    }

    public function handle_redirect($shortUrl) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pls_url_mappings';
        $originalUrl = $wpdb->get_var($wpdb->prepare("SELECT original_url FROM $table_name WHERE short_url = %s", $shortUrl));
        if ($originalUrl) {
            $wpdb->query($wpdb->prepare("UPDATE $table_name SET click_count = click_count + 1 WHERE short_url = %s", $shortUrl));
            wp_redirect($originalUrl);
            exit;
        }
    }

    public function enqueue_public_scripts() {
        wp_enqueue_script('post-link-shortener-public-js', PLS_PLUGIN_URL . 'public/js/post-link-shortener-public.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('post-link-shortener-public-css', PLS_PLUGIN_URL . 'public/css/post-link-shortener-public.css', array(), '1.0.0', 'all');
    }
}
