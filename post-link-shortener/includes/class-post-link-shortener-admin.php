<?php

class Post_Link_Shortener_Admin {

    public function run() {
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'Post Link Shortener',
            'Post Link Shortener',
            'manage_options',
            'post-link-shortener',
            array( $this, 'display_admin_page' ),
            'dashicons-admin-links'
        );
    }

    public function register_settings() {
        register_setting( 'post_link_shortener_settings', 'pls_custom_domain' );
    }

    public function display_admin_page() {
        ?>
        <div class="wrap">
            <h1>Post Link Shortener Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'post_link_shortener_settings' ); ?>
                <?php do_settings_sections( 'post_link_shortener_settings' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Custom Domain</th>
                        <td>
                            <input type="text" name="pls_custom_domain" value="<?php echo esc_attr( get_option('pls_custom_domain') ); ?>" placeholder="https://example.com" />
                            <p class="description">Enter the custom domain to use for shortened URLs.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
