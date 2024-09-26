<?php
/** 
 * OUQW Badge discount
 * 
 * @uses range_data
 * @uses discount_type_text
 * @uses badge_title
 * @uses badge_text
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
?>
<div class="ouqw-buymore-savemore">
    <div class="ouqw-badge">
        <div class="ouqw-badge-inner ouqw-flex ouqw_flex_align_items_center">
            <div>
                <img src="<?php echo esc_url(OUQW_PLUGIN_URL.'/assets/images/thanks.png') ?>" width="80" height="80">
            </div>    
            <div class="ouqw-badge-text" height="50">
                <p class="mb-0"><?php echo esc_html($badge_title); ?></p>
                <p class="mb-0"><?php echo esc_html($badge_text); ?></p>
            </div>
        </div>
        <ul class="ouqw-range-discount">
            <?php
            foreach ($range_data as $i => $tier) {
                if (empty($tier['rule_range_number']) || empty($tier['rule_discount_percent'])) {
                    continue;
                }
                
                $tier_number = absint( $tier['rule_range_number'] );
                $discount_percent = floatval( $tier['rule_discount_percent'] );

                $percent_off = sprintf('%1$s%% %2$s', $discount_percent, __('OFF', 'opal-upsale-quantity-for-woocommerce'));
                ?>
                <li class="ouqw-tier-discount">
                    <p class="mb-0">
                        <span class="ouqw-percent-discount"><?php echo esc_html($percent_off) ?></span>
                        <span class="ouqw-discount-text"><?php 
                        /* translators: %1$d: Tier number. */ 
                        /* translators: %2$s: Discount percent. */ 
                        printf(esc_html__('%1$d+ items get %2$s%% OFF', 'opal-upsale-quantity-for-woocommerce'), esc_html($tier_number), esc_html($discount_percent)); 
                        ?></span>
                        <span class="ouqw-discount-type-text"><?php echo esc_html($discount_type_text) ?></span>
                    </p>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
</div>