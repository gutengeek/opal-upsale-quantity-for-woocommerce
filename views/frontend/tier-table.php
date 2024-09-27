<?php
/** 
 * OUQW Custom Quantity
 * 
 * @uses raw_price
 * @uses range_data
 * @uses discount_type_text
 * @uses product_type
 * @uses show
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   

global $product;

?>
<div class="tier-table" style="<?php if(!$show) echo esc_attr('display: none'); ?>">
    <div class="ouqw-wrap ouqw-wrap-<?php echo esc_attr($product->get_id()); ?>" data-id="<?php echo esc_attr($product->get_id()); ?>">
        <div class="ouqw-table ouqw-table-<?php echo esc_attr($product->get_id()); ?>">
            <div class="ouqw-row ouqw-head">
                <div class="ouqw-row-qty"><?php esc_html_e('Quantity', 'opal-upsale-quantity-for-woocommerce') ?></div>
                <div class="ouqw-row-price"><?php esc_html_e('Price', 'opal-upsale-quantity-for-woocommerce') ?></div>
            </div>
            <?php
            $i = 0;
            foreach ($range_data as $tier) {
                if (empty($tier['rule_range_number']) || empty($tier['rule_discount_percent'])) {
                    continue;
                }
                $tier_number = absint( $tier['rule_range_number'] );
                $discount_percent = floatval( $tier['rule_discount_percent'] );

                $discount_price = $raw_price - ($raw_price * $discount_percent / 100);
                ?>
                <div class="ouqw-row ouqw-item ouqw-item-<?php echo esc_attr($i) ?>" data-discount_percent="<?php echo esc_attr($discount_percent) ?>" data-qty="<?php echo esc_attr($tier_number) ?>">
                    <div class="ouqw-item-qty"><?php echo esc_html($tier_number) ?>+ <strong><?php echo esc_html($discount_type_text) ?></strong></div>
                    <div class="ouqw-item-price">
                        <span class="ouqw-item-price-val">
                            <?php ouqw_print_price($discount_price) ?>
                        </span>
                        <span class="ouqw-item-text"><?php esc_html_e('each product', 'opal-upsale-quantity-for-woocommerce') ?></span>
                    </div>
                    <div class="ouqw-item-discount"><?php 
                    /* translators: %s: Discount percent. */ 
                    printf(esc_html__('Save %s%%', 'opal-upsale-quantity-for-woocommerce'), esc_html($discount_percent)) 
                    ?></div>
                </div>
                <?php
                $i++;
            }
            if ($product_type != 'grouped') {
                ?>
                <div class="ouqw-row ouqw-foot ouqw-summary">
                    <div class="ouqw-summary-info">
                        <span class="ouqw-summary-qty">1</span> ×
                        <span class="ouqw-summary-name"><?php echo esc_html(get_the_title()) ?></span>
                    </div>
                    <div class="ouqw-summary-total" data-raw_price="<?php echo esc_attr($raw_price) ?>">
                        <?php ouqw_print_price($raw_price) ?>
                    </div>
                </div>
                <?php
            }
            ?>
            
        </div>
        <!-- /ouqw-table -->
    </div>
</div>