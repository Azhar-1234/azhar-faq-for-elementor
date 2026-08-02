<?php
/**
 * Shared helpers for product level FAQ data.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'AZHAFAFO_FAQ_META_KEY' ) ) {
    define( 'AZHAFAFO_FAQ_META_KEY', '_azhafafo_product_faqs' );
}

/**
 * Clean up a raw FAQ array so every row has number, question and answer.
 *
 * Empty rows are dropped and missing numbers fall back to the row position.
 *
 * @param mixed $items Raw repeater rows.
 * @return array
 */
function azhafafo_normalize_faq_items( $items ) {
    $normalized = [];

    if ( ! is_array( $items ) ) {
        return $normalized;
    }

    $position = 0;

    foreach ( $items as $item ) {
        if ( ! is_array( $item ) ) {
            continue;
        }

        $question = isset( $item['question'] ) ? trim( (string) $item['question'] ) : '';
        $answer   = isset( $item['answer'] ) ? trim( (string) $item['answer'] ) : '';

        if ( '' === $question && '' === $answer ) {
            continue;
        }

        $position++;

        $number = isset( $item['number'] ) ? trim( (string) $item['number'] ) : '';

        $normalized[] = [
            'number'   => '' !== $number ? $number : (string) $position,
            'question' => $question,
            'answer'   => $answer,
        ];
    }

    return $normalized;
}

/**
 * Get the FAQ rows saved on a product.
 *
 * When no product id is passed the current product in the loop / query is used,
 * so the Elementor widget can stay unaware of the context it renders in.
 *
 * @param int $product_id Optional product id.
 * @return array Empty array when the product has no FAQ.
 */
function azhafafo_get_product_faqs( $product_id = 0 ) {
    $product_id = absint( $product_id );

    if ( ! $product_id ) {
        if ( function_exists( 'is_product' ) && is_product() ) {
            $product_id = get_queried_object_id();
        } else {
            $product_id = get_the_ID();
        }
    }

    if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
        return [];
    }

    $faqs = get_post_meta( $product_id, AZHAFAFO_FAQ_META_KEY, true );

    /**
     * Filter the FAQ rows loaded for a product.
     *
     * @param array $faqs       Normalized FAQ rows.
     * @param int   $product_id Product id.
     */
    return apply_filters( 'azhafafo_product_faqs', azhafafo_normalize_faq_items( $faqs ), $product_id );
}

/**
 * Build the FAQ accordion markup.
 *
 * @param array $items FAQ rows (already normalized).
 * @param array $args  Optional main_title / subtitle.
 * @return string
 */
function azhafafo_get_faq_html( $items, $args = [] ) {
    if ( empty( $items ) ) {
        return '';
    }

    $args = wp_parse_args(
        $args,
        [
            'main_title' => '',
            'subtitle'   => '',
        ]
    );

    ob_start();
    ?>
    <div class="dcc-bazar-faq-widget">
        <?php if ( '' !== $args['main_title'] || '' !== $args['subtitle'] ) : ?>
            <div class="dcc-faq-header" style="text-align: center; margin-bottom: 40px;">
                <?php if ( '' !== $args['main_title'] ) : ?>
                    <h2 class="dcc-faq-title" style="font-size: 2.25rem; font-weight: 700; margin-bottom: 12px;">
                        <?php echo esc_html( $args['main_title'] ); ?>
                    </h2>
                <?php endif; ?>
                <?php if ( '' !== $args['subtitle'] ) : ?>
                    <p class="dcc-faq-subtitle" style="font-size: 1.125rem;">
                        <?php echo esc_html( $args['subtitle'] ); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="dcc-faq-accordion">
            <?php foreach ( $items as $index => $item ) : ?>
                <div class="dcc-faq-item" data-index="<?php echo esc_attr( $index + 1 ); ?>">
                    <div class="dcc-question-header">

                        <div class="dcc-question-left">
                            <span class="dcc-number">
                                <?php echo esc_html( $item['number'] ); ?>
                            </span>
                            <h3 class="dcc-question">
                                <?php echo esc_html( $item['question'] ); ?>
                            </h3>
                        </div>

                        <span class="dcc-toggle-icon">+</span>

                    </div>
                    <div class="dcc-answer">
                        <?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php

    return (string) ob_get_clean();
}
