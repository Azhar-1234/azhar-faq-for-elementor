<?php
/**
 * Plugin Name:       Azhar FAQ for Elementor
 * Description:       FAQ accordion widget for Elementor, with a separate FAQ for every WooCommerce product.
 * Version:           1.1.0
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
    const VERSION = '1.1.0';

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
        $this->includes();
        add_action( 'init', [ $this, 'init' ] );
        add_action( 'plugins_loaded', [ $this, 'init_product_faq' ] );
        add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
    }

    /**
     * Load shared files
     */
    private function includes() {
        require_once AZHAFAFO_PLUGIN_PATH . '/includes/functions.php';

        if ( is_admin() ) {
            require_once AZHAFAFO_PLUGIN_PATH . '/includes/class-elementor-installer.php';
            new AZHAFAFO_Elementor_Installer();
        }
    }

    /**
     * Register the per product FAQ box. WooCommerce only, and independent of
     * Elementor so the saved data survives even if Elementor is disabled.
     */
    public function init_product_faq() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        require_once AZHAFAFO_PLUGIN_PATH . '/includes/class-product-faq-metabox.php';
        new AZHAFAFO_Product_FAQ_Metabox();
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
        // Available with or without Elementor
        add_action( 'wp_enqueue_scripts', [ $this, 'register_frontend_assets' ] );
        add_shortcode( 'azhar_product_faq', [ $this, 'product_faq_shortcode' ] );

        // Everything below needs Elementor. The install prompt is handled by
        // AZHAFAFO_Elementor_Installer, the product FAQ box keeps working.
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        // Register hooks
        add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
        add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Add plugin action links
     */
    public function plugin_action_links( $links ) {
        if ( class_exists( 'AZHAFAFO_Elementor_Installer' ) && ! AZHAFAFO_Elementor_Installer::is_active() ) {
            $link = '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . esc_html__( 'Install Elementor', 'azhar-faq-for-elementor' ) . '</a>';
        } else {
            $link = '<a href="' . esc_url( admin_url( 'admin.php?page=elementor-getting-started' ) ) . '">' . esc_html__( 'Settings', 'azhar-faq-for-elementor' ) . '</a>';
        }

        array_unshift( $links, $link );

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

    /**
     * Register the frontend assets so the shortcode works without Elementor
     */
    public function register_frontend_assets() {
        wp_register_style(
            'azhafafo-faq-style',
            AZHAFAFO_PLUGIN_URL . '/assets/css/style.css',
            [],
            self::VERSION
        );

        $this->register_scripts();
    }

    /**
     * Shortcode: [azhar_product_faq]
     *
     * Renders the FAQ saved on a product. Outputs nothing when the product has
     * no FAQ of its own.
     */
    public function product_faq_shortcode( $atts ) {
        $atts = shortcode_atts(
            [
                'id'       => 0,
                'title'    => '',
                'subtitle' => '',
            ],
            $atts,
            'azhar_product_faq'
        );

        $faqs = azhafafo_get_product_faqs( $atts['id'] );

        if ( empty( $faqs ) ) {
            return '';
        }

        wp_enqueue_style( 'azhafafo-faq-style' );
        wp_enqueue_script( 'azhafafo-faq-accordion' );

        return azhafafo_get_faq_html(
            $faqs,
            [
                'main_title' => $atts['title'],
                'subtitle'   => $atts['subtitle'],
            ]
        );
    }
}

/**
 * Initialize the plugin
 */
AZHAFAFO_Awesome_Elementor_FAQ::instance();
