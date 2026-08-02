<?php
/**
 * Adds a repeatable FAQ panel to the WooCommerce product data box.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AZHAFAFO_Product_FAQ_Metabox {

    /**
     * Nonce action / field name.
     */
    const NONCE_ACTION = 'azhafafo_save_product_faq';
    const NONCE_FIELD  = 'azhafafo_product_faq_nonce';

    /**
     * Name of the input array posted from the panel.
     */
    const INPUT_NAME = 'azhafafo_product_faq';

    /**
     * Register hooks.
     */
    public function __construct() {
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_tab' ] );
        add_action( 'woocommerce_product_data_panels', [ $this, 'render_panel' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'save' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Add the FAQ tab to the product data box.
     *
     * @param array $tabs Existing tabs.
     * @return array
     */
    public function add_tab( $tabs ) {
        $tabs['azhafafo_faq'] = [
            'label'    => esc_html__( 'FAQ', 'azhar-faq-for-elementor' ),
            'target'   => 'azhafafo_faq_product_data',
            'class'    => [],
            'priority' => 65,
        ];

        return $tabs;
    }

    /**
     * Only load the admin assets on the product edit screen.
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_assets( $hook ) {
        if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
            return;
        }

        $screen = get_current_screen();

        if ( ! $screen || 'product' !== $screen->post_type ) {
            return;
        }

        wp_enqueue_style(
            'azhafafo-admin-faq',
            AZHAFAFO_PLUGIN_URL . '/assets/css/admin.css',
            [],
            AZHAFAFO_Awesome_Elementor_FAQ::VERSION
        );

        wp_enqueue_script(
            'azhafafo-admin-faq',
            AZHAFAFO_PLUGIN_URL . '/assets/js/admin.js',
            [ 'jquery', 'jquery-ui-sortable' ],
            AZHAFAFO_Awesome_Elementor_FAQ::VERSION,
            true
        );

        wp_localize_script(
            'azhafafo-admin-faq',
            'azhafafoFaqAdmin',
            [
                'confirmRemove' => esc_html__( 'Remove this FAQ item?', 'azhar-faq-for-elementor' ),
                'newItemLabel'  => esc_html__( 'New FAQ item', 'azhar-faq-for-elementor' ),
            ]
        );
    }

    /**
     * Output the FAQ panel.
     */
    public function render_panel() {
        global $post;

        $faqs = $post ? azhafafo_get_product_faqs( $post->ID ) : [];
        ?>
        <div id="azhafafo_faq_product_data" class="panel woocommerce_options_panel azhafafo-faq-panel hidden">
            <div class="options_group">
                <p class="azhafafo-faq-intro">
                    <?php esc_html_e( 'Questions added here are shown on this product only. Leave it empty to keep the FAQ section unchanged.', 'azhar-faq-for-elementor' ); ?>
                </p>

                <?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>

                <div class="azhafafo-faq-rows">
                    <?php foreach ( $faqs as $index => $faq ) : ?>
                        <?php $this->render_row( $index, $faq ); ?>
                    <?php endforeach; ?>
                </div>

                <p class="azhafafo-faq-empty" <?php echo empty( $faqs ) ? '' : 'style="display:none;"'; ?>>
                    <?php esc_html_e( 'No FAQ added for this product yet.', 'azhar-faq-for-elementor' ); ?>
                </p>

                <p class="azhafafo-faq-actions">
                    <button type="button" class="button button-primary azhafafo-add-faq">
                        <?php esc_html_e( '+ Add FAQ', 'azhar-faq-for-elementor' ); ?>
                    </button>
                </p>
            </div>
        </div>

        <script type="text/html" id="tmpl-azhafafo-faq-row">
            <?php $this->render_row( '__INDEX__', [], true ); ?>
        </script>
        <?php
    }

    /**
     * Output a single repeater row.
     *
     * @param int|string $index       Row index, or the __INDEX__ placeholder.
     * @param array      $faq         Row values.
     * @param bool       $is_template Whether this is the JS template row.
     */
    private function render_row( $index, $faq = [], $is_template = false ) {
        $faq = wp_parse_args(
            $faq,
            [
                'number'   => '',
                'question' => '',
                'answer'   => '',
            ]
        );

        $base  = self::INPUT_NAME . '[' . $index . ']';
        $title = $is_template ? '' : $faq['question'];
        ?>
        <div class="azhafafo-faq-row">
            <div class="azhafafo-faq-row-head">
                <span class="azhafafo-faq-handle dashicons dashicons-menu"></span>
                <span class="azhafafo-faq-row-title">
                    <?php echo esc_html( '' !== $title ? $title : __( 'New FAQ item', 'azhar-faq-for-elementor' ) ); ?>
                </span>
                <button type="button" class="button-link azhafafo-toggle-faq" aria-label="<?php esc_attr_e( 'Toggle', 'azhar-faq-for-elementor' ); ?>">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <button type="button" class="button-link azhafafo-remove-faq" aria-label="<?php esc_attr_e( 'Remove', 'azhar-faq-for-elementor' ); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>

            <div class="azhafafo-faq-row-body">
                <p class="form-field azhafafo-field-number">
                    <label><?php esc_html_e( 'Number', 'azhar-faq-for-elementor' ); ?></label>
                    <input type="text" name="<?php echo esc_attr( $base . '[number]' ); ?>"
                           value="<?php echo esc_attr( $faq['number'] ); ?>"
                           placeholder="<?php esc_attr_e( 'Auto', 'azhar-faq-for-elementor' ); ?>" />
                    <span class="description"><?php esc_html_e( 'Leave empty to number automatically.', 'azhar-faq-for-elementor' ); ?></span>
                </p>

                <p class="form-field azhafafo-field-question">
                    <label><?php esc_html_e( 'Question', 'azhar-faq-for-elementor' ); ?></label>
                    <input type="text" class="azhafafo-question-input"
                           name="<?php echo esc_attr( $base . '[question]' ); ?>"
                           value="<?php echo esc_attr( $faq['question'] ); ?>" />
                </p>

                <p class="form-field azhafafo-field-answer">
                    <label><?php esc_html_e( 'Answer', 'azhar-faq-for-elementor' ); ?></label>
                    <textarea rows="4" name="<?php echo esc_attr( $base . '[answer]' ); ?>"><?php echo esc_textarea( $faq['answer'] ); ?></textarea>
                    <span class="description"><?php esc_html_e( 'Basic HTML is allowed.', 'azhar-faq-for-elementor' ); ?></span>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Persist the posted FAQ rows.
     *
     * @param int $post_id Product id.
     */
    public function save( $post_id ) {
        // Bail on quick edit / any request that did not render our panel, so the
        // existing FAQ data is never wiped by accident.
        if ( ! isset( $_POST[ self::NONCE_FIELD ] ) ) {
            return;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) );

        if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
            return;
        }

        $raw   = isset( $_POST[ self::INPUT_NAME ] ) ? wp_unslash( $_POST[ self::INPUT_NAME ] ) : [];
        $faqs  = [];

        if ( is_array( $raw ) ) {
            foreach ( $raw as $row ) {
                if ( ! is_array( $row ) ) {
                    continue;
                }

                $question = isset( $row['question'] ) ? sanitize_text_field( $row['question'] ) : '';
                $answer   = isset( $row['answer'] ) ? wp_kses_post( $row['answer'] ) : '';

                if ( '' === trim( $question ) && '' === trim( $answer ) ) {
                    continue;
                }

                $faqs[] = [
                    'number'   => isset( $row['number'] ) ? sanitize_text_field( $row['number'] ) : '',
                    'question' => $question,
                    'answer'   => $answer,
                ];
            }
        }

        if ( empty( $faqs ) ) {
            delete_post_meta( $post_id, AZHAFAFO_FAQ_META_KEY );
            return;
        }

        update_post_meta( $post_id, AZHAFAFO_FAQ_META_KEY, $faqs );
    }
}
