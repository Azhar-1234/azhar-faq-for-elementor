<?php
/**
 * One click install / activate for the required Elementor plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AZHAFAFO_Elementor_Installer {

    const SLUG        = 'elementor';
    const PLUGIN_FILE = 'elementor/elementor.php';
    const ACTION      = 'azhafafo_install_elementor';
    const NONCE       = 'azhafafo_install_elementor_nonce';

    /**
     * Register hooks.
     */
    public function __construct() {
        add_action( 'admin_notices', [ $this, 'notice' ] );
        add_action( 'admin_post_' . self::ACTION, [ $this, 'handle_request' ] );
    }

    /**
     * Is Elementor active?
     */
    public static function is_active() {
        return did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' );
    }

    /**
     * Is Elementor present on disk but not activated?
     */
    private function is_installed() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        return isset( $plugins[ self::PLUGIN_FILE ] );
    }

    /**
     * Can this install write to the plugins folder without asking for credentials?
     */
    private function has_direct_filesystem() {
        if ( ! function_exists( 'get_filesystem_method' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        return 'direct' === get_filesystem_method();
    }

    /**
     * Admin notice with the install / activate button.
     */
    public function notice() {
        $this->maybe_show_result_notice();

        if ( self::is_active() || ! current_user_can( 'install_plugins' ) ) {
            return;
        }

        $installed = $this->is_installed();

        if ( $installed ) {
            $message = esc_html__( '"Azhar FAQ for Elementor" needs Elementor. It is already installed, just activate it.', 'azhar-faq-for-elementor' );
            $button  = esc_html__( 'Activate Elementor', 'azhar-faq-for-elementor' );
        } else {
            $message = esc_html__( '"Azhar FAQ for Elementor" needs Elementor to be installed and activated.', 'azhar-faq-for-elementor' );
            $button  = esc_html__( 'Install & Activate Elementor', 'azhar-faq-for-elementor' );
        }

        // Without direct filesystem access the upgrader would ask for FTP
        // credentials, which an admin-post redirect cannot handle. Send the user
        // to the normal WordPress installer instead.
        if ( ! $installed && ! $this->has_direct_filesystem() ) {
            $url = admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' );
        } else {
            $url = wp_nonce_url(
                admin_url( 'admin-post.php?action=' . self::ACTION ),
                self::ACTION,
                self::NONCE
            );
        }

        printf(
            '<div class="notice notice-warning"><p>%s</p><p><a href="%s" class="button button-primary">%s</a></p></div>',
            esc_html( $message ),
            esc_url( $url ),
            esc_html( $button )
        );
    }

    /**
     * Show the outcome of the last install attempt.
     */
    private function maybe_show_result_notice() {
        $key    = 'azhafafo_elementor_install_result_' . get_current_user_id();
        $result = get_transient( $key );

        if ( ! $result ) {
            return;
        }

        delete_transient( $key );

        printf(
            '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
            'success' === $result['type'] ? 'success' : 'error',
            esc_html( $result['message'] )
        );
    }

    /**
     * Store a message for the next page load.
     */
    private function set_result( $type, $message ) {
        set_transient(
            'azhafafo_elementor_install_result_' . get_current_user_id(),
            [
                'type'    => $type,
                'message' => $message,
            ],
            60
        );
    }

    /**
     * Send the user back where they came from.
     */
    private function redirect_back() {
        $referer = wp_get_referer();

        wp_safe_redirect( $referer ? $referer : admin_url( 'plugins.php' ) );
        exit;
    }

    /**
     * Install and/or activate Elementor.
     */
    public function handle_request() {
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'You are not allowed to install plugins.', 'azhar-faq-for-elementor' ) );
        }

        check_admin_referer( self::ACTION, self::NONCE );

        if ( self::is_active() ) {
            $this->redirect_back();
        }

        if ( ! $this->is_installed() ) {
            $installed = $this->install();

            if ( is_wp_error( $installed ) ) {
                $this->set_result(
                    'error',
                    sprintf(
                        /* translators: %s: error message */
                        __( 'Elementor could not be installed: %s', 'azhar-faq-for-elementor' ),
                        $installed->get_error_message()
                    )
                );

                $this->redirect_back();
            }
        }

        $activated = activate_plugin( self::PLUGIN_FILE );

        if ( is_wp_error( $activated ) ) {
            $this->set_result(
                'error',
                sprintf(
                    /* translators: %s: error message */
                    __( 'Elementor could not be activated: %s', 'azhar-faq-for-elementor' ),
                    $activated->get_error_message()
                )
            );
        } else {
            $this->set_result( 'success', __( 'Elementor is installed and active. FAQ widget is ready to use.', 'azhar-faq-for-elementor' ) );
        }

        $this->redirect_back();
    }

    /**
     * Download and install Elementor from the WordPress.org repository.
     *
     * @return true|WP_Error
     */
    private function install() {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        if ( ! $this->has_direct_filesystem() ) {
            return new WP_Error(
                'azhafafo_no_direct_fs',
                __( 'WordPress needs filesystem credentials on this server. Please install Elementor from Plugins > Add New.', 'azhar-faq-for-elementor' )
            );
        }

        $api = plugins_api(
            'plugin_information',
            [
                'slug'   => self::SLUG,
                'fields' => [ 'sections' => false ],
            ]
        );

        if ( is_wp_error( $api ) ) {
            return $api;
        }

        $skin     = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $result   = $upgrader->install( $api->download_link );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        if ( is_wp_error( $skin->result ) ) {
            return $skin->result;
        }

        if ( true !== $result ) {
            $errors = $skin->get_error_messages();

            return new WP_Error(
                'azhafafo_install_failed',
                $errors ? implode( ' ', $errors ) : __( 'Unknown error.', 'azhar-faq-for-elementor' )
            );
        }

        return true;
    }
}
