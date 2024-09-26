<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    

$validates_message = [
    'badge_title' => [
        'label' => esc_html__('Badge Title', 'opal-upsale-quantity-for-woocommerce'),
        'value' => esc_html__('Buy More Save More!', 'opal-upsale-quantity-for-woocommerce'),
        'placeholder' => esc_html__('Content', 'opal-upsale-quantity-for-woocommerce'),
    ],
    'badge_text' => [
        'label' => esc_html__('Badge Text', 'opal-upsale-quantity-for-woocommerce'),
        'value' => esc_html__("It is time to give thanks for all the little things.", 'opal-upsale-quantity-for-woocommerce'),
        'placeholder' => esc_html__('Content', 'opal-upsale-quantity-for-woocommerce')
    ],
    'discount_by_product_items' => [
        'label' => esc_html__('Discount Text by number of product items', 'opal-upsale-quantity-for-woocommerce'),
        'value' => __('on number of product items', 'opal-upsale-quantity-for-woocommerce'),
        'placeholder' => esc_html__('Content', 'opal-upsale-quantity-for-woocommerce')
    ],
    'discount_by_cart_items' => [
        'label' => esc_html__('Discount Text by number of cart items', 'opal-upsale-quantity-for-woocommerce'),
        'value' => __('on cart total', 'opal-upsale-quantity-for-woocommerce'),
        'placeholder' => esc_html__('Content', 'opal-upsale-quantity-for-woocommerce')
    ],
];