<?php
/** 
 * OUQW Badge discount
 * 
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
    </div>
</div>