<?php
/** 
 * OUQW Custom Quantity
 * 
 * @uses raw_price
 * @uses range_data
 * @uses discount_type_text
 * @uses badge_title
 * @uses badge_text
 * @uses show
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   

global $product;
?>
<div class="tier-line">
    <ul class="ouqw-range-discount">
        <?php
        $i = 0;
        foreach ($range_data as $tier) {
            if (empty($tier['rule_range_number']) || empty($tier['rule_discount_percent'])) {
                continue;
            }
            
            $tier_number = absint( $tier['rule_range_number'] );
            $discount_percent = floatval( $tier['rule_discount_percent'] );
    
            $percent_off = sprintf('%1$s%% %2$s', $discount_percent, __('OFF', 'opal-upsale-quantity-for-woocommerce'));
            ?>
            <li class="ouqw-tier-discount ouqw-tier-<?php echo esc_attr($i) ?>" data-discount_percent="<?php echo esc_attr($discount_percent) ?>" data-qty="<?php echo esc_attr($tier_number) ?>">
                <p class="mb-0">
                    <span class="ouqw-percent-discount"><span><?php echo esc_html($percent_off) ?></span></span>
                    <span class="ouqw-discount-text"><?php 
                    /* translators: %1$d: Tier number. */ 
                    /* translators: %2$s: Discount percent. */ 
                    printf(esc_html__('%1$d+ items get %2$s%% OFF', 'opal-upsale-quantity-for-woocommerce'), esc_html($tier_number), esc_html($discount_percent)); 
                    ?></span>
                    <span class="ouqw-discount-type-text"><?php echo esc_html($discount_type_text) ?></span>
                </p>
            </li>
            <?php
            $i++;
        }
        ?>
    </ul>
</div>