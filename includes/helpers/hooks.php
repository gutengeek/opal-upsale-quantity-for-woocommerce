<?php
/**
 * =================================================
 * Hook ouqw_before_upsale_discount_shortcode
 * =================================================
 */
add_action('ouqw_before_upsale_discount_shortcode', 'ouqw_wrapper_open_upsale_view', 10);

/**
 * =================================================
 * Hook ouqw_after_upsale_discount_shortcode
 * =================================================
 */
add_action('ouqw_after_upsale_discount_shortcode', 'ouqw_wrapper_close_upsale_view', 10);

/**
 * =================================================
 * Hook ouqw_before_upsale_discount_view
 * =================================================
 */
add_action('ouqw_before_upsale_discount_view', 'ouqw_wrapper_open_upsale_view', 10);

/**
 * =================================================
 * Hook ouqw_after_upsale_discount_view
 * =================================================
 */
add_action('ouqw_after_upsale_discount_view', 'ouqw_wrapper_close_upsale_view', 10);
