<?php
/** 
 * OUQW Custom Quantity
 * 
 * @uses raw_price
 * @uses range_data
 * @uses discount_type_text
 * @uses product_type
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   

global $product;
?>
<div class="ouqw-tier-qty">
    <label class="quantity_tier"><?php esc_html_e('Quantity:', 'opal-upsale-quantity-for-woocommerce') ?></label>
    <div class="ouqw-dropdown-tiers">
        <div class="wrapper-tier-action">
            <div class="tier-value">1</div>
            <?php
            OUQW_Frontend::view('tier-table', [
                'raw_price' => $raw_price,
                'range_data' => $range_data,
                'discount_type_text' => $discount_type_text,
                'product_type' => $product_type,
                'show' => false
            ]);
            ?>
        </div>
    </div>
</div>
