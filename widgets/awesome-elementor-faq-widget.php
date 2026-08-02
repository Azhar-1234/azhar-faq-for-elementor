<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AZHAFAFO_Elementor_FAQ_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'awesome_elementor_faq';
    }

    public function get_title() {
    return esc_html__( 'Azhar FAQ for Elementor', 'azhar-faq-for-elementor' );
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return [ 'general', 'dcc-bazar' ];
    }

    public function get_keywords() {
        return [ 'faq', 'accordion', 'dcc', 'bazar', 'question', 'dcc bazar' ];
    }

    protected function register_controls() {

        // ==================== CONTENT ====================
        $this->start_controls_section(
            'section_header',
            [
                'label' => esc_html__( 'Header', 'azhar-faq-for-elementor' ),
            ]
        );

        $this->add_control(
            'main_title',
            [
                'label' => esc_html__( 'Main Title', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Frequently Ask.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => esc_html__( 'Subtitle', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Find answers to common questions about our products and services',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // FAQ Items
        $this->start_controls_section(
            'section_faq_items',
            [
                'label' => esc_html__( 'FAQ Items', 'azhar-faq-for-elementor' ),
            ]
        );

        $this->add_control(
            'faq_source',
            [
                'label' => esc_html__( 'FAQ Source', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto'    => esc_html__( 'Product FAQ, fallback to items below', 'azhar-faq-for-elementor' ),
                    'product' => esc_html__( 'Product FAQ only', 'azhar-faq-for-elementor' ),
                    'manual'  => esc_html__( 'Items below only', 'azhar-faq-for-elementor' ),
                ],
                'description' => esc_html__( 'Product FAQ comes from the FAQ tab of the WooCommerce product being viewed.', 'azhar-faq-for-elementor' ),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'number',
            [
                'label' => esc_html__( 'Number', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '1',
            ]
        );

        $repeater->add_control(
            'question',
            [
                'label' => esc_html__( 'Question', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'GoBaby কী?',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'answer',
            [
                'label' => esc_html__( 'Answer', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => 'এখানে উত্তর লিখুন...',
            ]
        );

        $this->add_control(
            'faq_items',
            [
                'label' => esc_html__( 'FAQ Items', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'number' => '1',
                        'question' => 'GoBaby কী?',
                        'answer' => 'GoBaby হলো আমাদের প্রিমিয়াম প্রোডাক্ট সিরিজ...',
                    ],
                    [
                        'number' => '2',
                        'question' => 'কিভাবে Order দিবো & Payment Options কী?',
                        'answer' => 'অর্ডার করার বিস্তারিত নিয়ম...',
                    ],
                ],
                'title_field' => '{{{ number }}}. {{{ question }}}',
                'condition' => [
                    'faq_source!' => 'product',
                ],
            ]
        );

        $this->end_controls_section();

        // ==================== STYLE ====================

        // Header Style
        $this->start_controls_section(
            'section_header_style',
            [
                'label' => esc_html__( 'Header Style', 'azhar-faq-for-elementor' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a1a1a',
                'selectors' => [
                    '{{WRAPPER}} .dcc-faq-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__( 'Title Typography', 'azhar-faq-for-elementor' ),
                'selector' => '{{WRAPPER}} .dcc-faq-title',
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => esc_html__( 'Subtitle Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .dcc-faq-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'label' => esc_html__( 'Subtitle Typography', 'azhar-faq-for-elementor' ),
                'selector' => '{{WRAPPER}} .dcc-faq-subtitle',
            ]
        );

        $this->end_controls_section();

        // Accordion Style
        $this->start_controls_section(
            'section_accordion_style',
            [
                'label' => esc_html__( 'Accordion Style', 'azhar-faq-for-elementor' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_bg',
            [
                'label' => esc_html__( 'Item Background', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .dcc-faq-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => esc_html__( 'Border Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e5e5e5',
                'selectors' => [
                    '{{WRAPPER}} .dcc-faq-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'question_color',
            [
                'label' => esc_html__( 'Question Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#222222',
                'selectors' => [
                    '{{WRAPPER}} .dcc-question' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'question_typography',
                'label' => esc_html__( 'Question Typography', 'azhar-faq-for-elementor' ),
                'selector' => '{{WRAPPER}} .dcc-question',
            ]
        );

        $this->add_control(
            'number_color',
            [
                'label' => esc_html__( 'Number Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .dcc-number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => [
                    '{{WRAPPER}} .dcc-toggle-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'active_color',
            [
                'label' => esc_html__( 'Active Question Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e6007e',
                'selectors' => [
                    '{{WRAPPER}} .dcc-faq-item.active .dcc-question' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'answer_color',
            [
                'label' => esc_html__( 'Answer Color', 'azhar-faq-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .dcc-answer' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'answer_typography',
                'label' => esc_html__( 'Answer Typography', 'azhar-faq-for-elementor' ),
                'selector' => '{{WRAPPER}} .dcc-answer',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $source   = ! empty( $settings['faq_source'] ) ? $settings['faq_source'] : 'auto';

        $items = [];

        // Product FAQ wins when the current product has one of its own.
        if ( 'manual' !== $source ) {
            $items = azhafafo_get_product_faqs();
        }

        // Otherwise fall back to the items configured on the widget.
        if ( empty( $items ) && 'product' !== $source ) {
            $items = azhafafo_normalize_faq_items( $settings['faq_items'] );
        }

        if ( empty( $items ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p>' . esc_html__( 'No FAQ to show. Add FAQ items to this product, or switch the FAQ Source.', 'azhar-faq-for-elementor' ) . '</p>';
            }
            return;
        }

        // Escaped inside azhafafo_get_faq_html().
        echo azhafafo_get_faq_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            $items,
            [
                'main_title' => $settings['main_title'],
                'subtitle'   => $settings['subtitle'],
            ]
        );
    }
}