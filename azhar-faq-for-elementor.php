<?php
/**
 * Plugin Name:       Azhar FAQ for Elementor
 * Description:       A simple and awesome FAQ accordion widget for Elementor.
 * Version:           1.0.0
 * Author:            Azhar Uddin
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       azhar-faq-for-elementor
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * The main plugin class
 */
final class AZHAFAFO_Awesome_Elementor_FAQ {

    /**
     * Plugin Version
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * The single instance of the class
     *
    * @var AZHAFAFO_Awesome_Elementor_FAQ
     */
    private static $_instance = null;

    /**
     * Ensures only one instance of the class is loaded
     *
    * @return AZHAFAFO_Awesome_Elementor_FAQ - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->define_constants();
        add_action( 'init', [ $this, 'init' ] );
        add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
    }

    /**
     * Define Plugin Constants
     */
    private function define_constants() {
    define( 'AZHAFAFO_PLUGIN_FILE', __FILE__ );
    define( 'AZHAFAFO_PLUGIN_PATH', __DIR__ );
    define( 'AZHAFAFO_PLUGIN_URL', plugins_url( '', AZHAFAFO_PLUGIN_FILE ) );
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if Elementor is loaded
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
            return;
        }

        // Register hooks
        add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
        add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Admin notice for missing Elementor
     */
    public function admin_notice_missing_elementor() {
        $elementor_link = esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) );
        $message = sprintf(
            /* translators: %1$s: Plugin name, %2$s: Elementor, %3$s: install URL */
            __( '"%1$s" requires "%2$s" to be installed and activated. <a href="%3$s">Install Elementor</a>.', 'azhar-faq-for-elementor' ),
            '<strong>' . esc_html__( 'Azhar FAQ for Elementor', 'azhar-faq-for-elementor' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'azhar-faq-for-elementor' ) . '</strong>',
            $elementor_link
        );

        $allowed = [
            'a'      => [ 'href' => true ],
            'strong' => [],
        ];

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            wp_kses( $message, $allowed )
        );
    }

    /**
     * Add plugin action links
     */
    public function plugin_action_links( $links ) {
    $settings_link = '<a href="admin.php?page=elementor-getting-started">' . esc_html__( 'Settings', 'azhar-faq-for-elementor' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Register the widget
     */
    public function register_widget( $widgets_manager ) {
    require_once( AZHAFAFO_PLUGIN_PATH . '/widgets/awesome-elementor-faq-widget.php' );
    $widgets_manager->register( new \AZHAFAFO_Elementor_FAQ_Widget() );
    }

    /**
     * Enqueue styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'azhafafo-faq-style',
            AZHAFAFO_PLUGIN_URL . '/assets/css/style.css',
            [],
            self::VERSION
        );
    }

    /**
     * Register scripts
     */
    public function register_scripts() {
        wp_register_script(
            'azhafafo-faq-accordion',
            AZHAFAFO_PLUGIN_URL . '/assets/js/accordion.js',
            [],
            self::VERSION,
            true
        );
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
    wp_enqueue_script( 'azhafafo-faq-accordion' );
    }
}

/**
 * Initialize the plugin
 */
AZHAFAFO_Awesome_Elementor_FAQ::instance();
