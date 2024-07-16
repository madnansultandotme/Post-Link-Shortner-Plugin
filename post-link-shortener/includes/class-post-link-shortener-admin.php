<?php

class Post_Link_Shortener_Admin {

    public function run() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page() {
        add_options_page(
            'Post Link Shortener', 
            'Post Link Shortener', 
            'manage_options', 
            'post-link-shortener', 
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1>Post Link Shortener</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'pls_option_group' );
                do_settings_sections( 'post-link-shortener-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'pls_option_group',
            'pls_custom_domain',
            array( 'sanitize_callback' => 'sanitize_text_field', 'default' => '' )
        );

        add_settings_section(
            'setting_section_id',
            'Settings',
            null,
            'post-link-shortener-admin'
        );

        add_settings_field(
            'pls_custom_domain',
            'Custom Domain',
            array( $this, 'custom_domain_callback' ),
            'post-link-shortener-admin',
            'setting_section_id'
        );
    }

    public function custom_domain_callback() {
        $customDomain = get_option('pls_custom_domain');
        printf(
            '<input type="text" id="pls_custom_domain" name="pls_custom_domain" value="%s" />',
            isset( $customDomain ) ? esc_attr( $customDomain ) : ''
        );
    }
}
