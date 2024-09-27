<?php
/** 
 * OUQW Settings Page
 * 
 * @uses settings
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    

$fb_class = '';
?>
<div class="wrap">
    <div class="ouqw_header_settings">
        <h2 class="ouqw_title_page"><?php esc_html_e('Settings', 'opal-upsale-quantity-for-woocommerce') ?></h2>
        <h3 class="ouqw_subtitle_page"><?php esc_html_e('Upsale Quantity Price', 'opal-upsale-quantity-for-woocommerce') ?></h3>
    </div>
</div>
<div class="wrap ouqw_wrap_settings">
    <ul class="ouqw_g_set_tabs <?php echo esc_html($fb_class); ?>">
        <li>
            <a href="#ouqw_display_settings" class="active">
                <img src="<?php echo esc_url(OUQW_PLUGIN_URL.'/assets/images/display-settings.svg') ?>" width="20" height="20" alt=""><?php esc_html_e('Settings', 'opal-upsale-quantity-for-woocommerce'); ?>
            </a>
        </li>
        <li>
            <a href="#ouqw_import_export">
                <img src="<?php echo esc_url(OUQW_PLUGIN_URL.'/assets/images/backup-settings.svg') ?>" width="20" height="20" alt=""><?php esc_html_e('Import/Export Settings', 'opal-upsale-quantity-for-woocommerce'); ?>
            </a>
        </li>
    </ul>
    <div class="ouqw_g_set_tabcontents <?php echo esc_html($fb_class); ?>">
        <div class="ouqw_wrap_tabcontent">
            <div id="ouqw_display_settings" class="ouqw_tabcontent">
                <div class="options_group">
                    <h3><?php esc_html_e('Display Sale Off', 'opal-upsale-quantity-for-woocommerce') ?></h3>
                    <ul>
                        <li class="option_item ouqw_group_settings_mt">
                        <?php
                            woocommerce_wp_select(
                                array(
                                    'id'          => 'product_render_position',
                                    'value'       => ouqw_get_option('product_render_position', '', $settings),
                                    'label'       => __( 'Position on single product ', 'opal-upsale-quantity-for-woocommerce' ),
                                    'options'     => array(
                                        '' => __( 'Hidden', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_before_add_to_cart_button-10' => __( 'Before "Add to cart" button - 10', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_after_add_to_cart_button-5'  => __( 'After "Add to cart" button - 5', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_before_add_to_cart_quantity-5'  => __( 'Before "Quantity" - 5', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_after_add_to_cart_quantity-5'  => __( 'After "Quantity" - 5', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_before_variations_form-5'  => __( 'Before "Variation fields" (Only Variable Products) - 5', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_after_variations_table-5'  => __( 'After "Variation fields" (Only Variable Products) - 5', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-4'  => __( 'Before "Title" - 4', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-5'  => __( 'After "Title" - 5', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-19'  => __( 'Before "Excerpt" - 19', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-20'  => __( 'After "Excerpt" - 20', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-9'  => __( 'Before "Price" - 9', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-10'  => __( 'After "Price" - 10', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-29'  => __( 'Before "Add to cart" form - 29', 'opal-upsale-quantity-for-woocommerce' ),
                                        'woocommerce_single_product_summary-30'  => __( 'After "Add to cart" form - 30', 'opal-upsale-quantity-for-woocommerce' ),
                                    ),
                                    'wrapper_class' => 'ouqw_setting_form', 
                                    'class' => 'ouqw_setting_field',
                                    'style' => 'width:100%;margin-left:0'
                                )
                            );
                        ?>
                        </li>
                        <li>
                        <?php
                            woocommerce_wp_text_input(
                                array(
                                    'id'          => 'render_position_prioty',
                                    'class' => 'ouqw_setting_field',
                                    'wrapper_class' => 'ouqw_setting_form',
                                    'label'       => esc_html__( 'Prioty: ', 'opal-upsale-quantity-for-woocommerce' ),
                                    'placeholder' => '5',
                                    'value'       => ouqw_get_option('render_position_prioty', '', $settings),
                                    'style' => 'display: block'
                                )
                            );
                        ?>
                        </li>
                        <li class="option_item ouqw_group_settings_mt">
                        <?php
                            woocommerce_wp_select(
                                array(
                                    'id'          => 'product_render_type',
                                    'value'       => ouqw_get_option('product_render_type', '', $settings),
                                    'label'       => __( 'Discount show type', 'opal-upsale-quantity-for-woocommerce' ),
                                    'options'     => array(
                                        'tier-line' => __( 'Tier line', 'opal-upsale-quantity-for-woocommerce' ),
                                        'tier-table' => __( 'Tier table', 'opal-upsale-quantity-for-woocommerce' ),
                                    ),
                                    'wrapper_class' => 'ouqw_setting_form', 
                                    'class' => 'ouqw_setting_field',
                                    'style' => 'width:100%;margin-left:0'
                                )
                            );
                        ?>
                        </li>
                    </ul>
                </div>
                <div class="options_group">
                    <ul>
                        <li>
                            <?php
                            ouqw_wp_checkbox( array( 
                                'wrapper_class' => 'ouqw_setting_form ouqw_flex_row_reverse ouqw_flex_align_items_center', 
                                'id' => 'show_badge',
                                'class' => 'ouqw_setting_field',
                                'label' => esc_html__('Show Badge', 'opal-upsale-quantity-for-woocommerce'),
                                'value' => ouqw_get_option('show_badge', 0, $settings),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                        <li>
                            <?php
                            ouqw_wp_checkbox( array( 
                                'wrapper_class' => 'ouqw_setting_form ouqw_flex_row_reverse ouqw_flex_align_items_center', 
                                'id' => 'custom_quantity_input',
                                'class' => 'ouqw_setting_field',
                                'label' => esc_html__('Custom Quantity Input', 'opal-upsale-quantity-for-woocommerce'),
                                'value' => ouqw_get_option('custom_quantity_input', 0, $settings),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                        <li>
                            <?php
                            ouqw_wp_checkbox( array( 
                                'wrapper_class' => 'ouqw_setting_form ouqw_flex_row_reverse ouqw_flex_align_items_center', 
                                'id' => 'show_in_cart_item',
                                'class' => 'ouqw_setting_field',
                                'label' => esc_html__('Show in cart and checkout', 'opal-upsale-quantity-for-woocommerce'),
                                'value' => ouqw_get_option('show_in_cart_item', 0, $settings),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                        <li>
                            <?php
                            ouqw_wp_checkbox( array( 
                                'wrapper_class' => 'ouqw_setting_form ouqw_flex_row_reverse ouqw_flex_align_items_center', 
                                'id' => 'show_in_order',
                                'class' => 'ouqw_setting_field',
                                'label' => esc_html__('Show in order', 'opal-upsale-quantity-for-woocommerce'),
                                'value' => ouqw_get_option('show_in_order', 0, $settings),
                                'cbvalue' => 1,
                                'checkbox_ui' => true
                            ) );
                            ?>
                        </li>
                    </ul>
                </div>
                <div class="options_group">
                    <h3><?php esc_html_e('Rules', 'opal-upsale-quantity-for-woocommerce') ?></h3>
                    <ul>
                        <li class="option_item ouqw_group_settings_mt">
                        <?php
                            $rule_apply_for = ouqw_get_option('rule_apply_for', 'all', $settings);
                            $rule_apply_select_val = ouqw_get_option('rule_apply_select_val', '', $settings);
                            $select_val = [];
                            if (is_array($rule_apply_select_val) && !empty($rule_apply_select_val)) {
                                foreach ($rule_apply_select_val as $id) {
                                    switch ($rule_apply_for) {
                                        case 'product':
                                            $select_val[$id] = get_the_title($id);
                                            break;
                                        case 'category':
                                            $term = get_term($id);
                                            $select_val[$id] = (is_wp_error($term) || !$term) ? $id : $term->name;
                                            break;
                                        case 'tag':
                                            $term = get_term($id);
                                            $select_val[$id] = (is_wp_error($term) || !$term) ? $id : $term->name;
                                            break;
                                        case 'shipping_class':
                                            $term = get_term($id);
                                            $select_val[$id] = (is_wp_error($term) || !$term) ? $id : $term->name;
                                            break;
                                        default:
                                            $types = wc_get_product_types();
                                            if ($types && isset($types[$id])) {
                                                $select_val[$id] = $types[$id];
                                            }
                                            break;
                                    }
                                }
                            }

                            woocommerce_wp_select(
                                array(
                                    'id'          => 'rule_apply_for',
                                    'value'       => $rule_apply_for,
                                    'label'       => __( 'Rule aplly for:', 'opal-upsale-quantity-for-woocommerce' ),
                                    'options'     => [
                                        'all' => __( 'All product', 'opal-upsale-quantity-for-woocommerce' ),
                                        'instock' => __( 'In stock', 'opal-upsale-quantity-for-woocommerce' ),
                                        'outofstock' => __( 'Out of stock', 'opal-upsale-quantity-for-woocommerce' ),
                                        'onbackorder' => __( 'On backorder', 'opal-upsale-quantity-for-woocommerce' ),
                                        'product' => __( 'Some products', 'opal-upsale-quantity-for-woocommerce' ),
                                        'category' => __( 'Product category', 'opal-upsale-quantity-for-woocommerce' ),
                                        'tag' => __( 'Product tag', 'opal-upsale-quantity-for-woocommerce' ),
                                        'type' => __( 'Product type', 'opal-upsale-quantity-for-woocommerce' ),
                                    ],
                                    'wrapper_class' => 'ouqw_setting_form', 
                                    'class' => 'ouqw_setting_field ouqw_rule_apply_for',
                                    'style' => 'width:100%;margin-left:0',
                                )
                            );

                            $wrap_class = 'ouqw_setting_form ouqw_field_nolabel ouqw_wrapper_rules_apply';
                            if (in_array($rule_apply_for, ['all', 'instock', 'outstock', 'backorder', ''])) {
                                $wrap_class .= ' ouqw_hidden';
                            }
                            woocommerce_wp_select(
                                array(
                                    'id'          => 'rule_apply_select_val',
                                    'value'       => $rule_apply_select_val,
                                    'options'     => $select_val,
                                    'wrapper_class' => $wrap_class, 
                                    'label' => '',
                                    'class' => 'ouqw_setting_field ouqw_rules_apply ouqw_init_select2',
                                    'style' => 'width:95%;margin-left:0',
                                    'custom_attributes' => [
                                        'multiple' => "multiple",
                                        'data-placeholder' => __( 'Typing to select', 'opal-upsale-quantity-for-woocommerce' ),
                                    ]
                                )
                            );

                            woocommerce_wp_select(
                                array(
                                    'id'          => 'discount_type',
                                    'value'       => ouqw_get_option('discount_type', '', $settings),
                                    'label'       => __( 'Discount Type:', 'opal-upsale-quantity-for-woocommerce' ),
                                    'options'     => [
                                        'product_items' => __( 'Base on number of Product items', 'opal-upsale-quantity-for-woocommerce' ),
                                        'cart_items' => __( 'Base on number of Cart items', 'opal-upsale-quantity-for-woocommerce' ),
                                    ],
                                    'wrapper_class' => 'ouqw_setting_form', 
                                    'class' => 'ouqw_setting_field ouqw_rule_apply_for',
                                    'style' => 'width:100%;margin-left:0',
                                )
                            );
                        ?>
                        </li>
                    </ul>
                    <div class="ouqw_gr_rules" id="ouqw_rules_settings">
                        <h4><?php esc_html_e('Rules Range', 'opal-upsale-quantity-for-woocommerce') ?></h4>
                        <div class="ouqw_wrapper_rules">
                            <?php
                            $rules_range = ouqw_get_option('rules_range', [], $settings);
                            if (!empty($rules_range) && is_array($rules_range)) {
                                foreach ($rules_range as $i => $rule_item) {
                                    OUQW_Settings::view('rule-item', ['rule_item' => $rule_item, 'index' => $i]);
                                }
                            }
                            else {
                                OUQW_Settings::view('rule-item', ['rule_item' => [], 'index' => 0]);
                            }
                            ?>
                            <nav class="repeater_btn ouqw-flex"><a href="javascript:void(0)" class="button rpt_btn_add"><?php esc_html_e('+ Add Rule', 'opal-upsale-quantity-for-woocommerce') ?></a></nav>
                        </div>
                    </div>
                </div>
                <div class="options_group">
                    <h3><?php esc_html_e('Shortcode', 'opal-upsale-quantity-for-woocommerce') ?></h3>
                    <p>
                        <?php  
                        echo wp_kses('You can use shortcode <code>[ouqw]</code> to show the discount range by quantity for current product.', ['code' => []]);
                        ?>
                    </p>
                    <p>
                        <?php  
                        echo wp_kses('Or you can also use the product id in the shortcode to show the discount range by quantity for a specific product. For example:<code>[ouqw id="123"]</code>', ['code' => []]);
                        ?>
                    </p>
                </div>
                <div class="options_group">
                    <h3><?php esc_html_e('Contents/Strings', 'opal-upsale-quantity-for-woocommerce') ?></h3>
                    <ul>
                        <?php
                        require OUQW_PLUGIN_DIR.'includes/helpers/define.php';

                        $validates_message = apply_filters('ouqw_validates_message_custom', $validates_message);
                        foreach ($validates_message as $name => $message) {
                            ?>
                            <li>
                            <?php
                            woocommerce_wp_text_input(
                                array(
                                    'id'            => $name,
                                    'class' => 'ouqw_setting_field',
                                    'wrapper_class' => 'ouqw_setting_form', 
                                    'label'         => isset($message['label']) ? $message['label'] : '',
                                    'value'         => ouqw_get_option($name, '', $settings),
                                    'placeholder'   => isset($message['placeholder']) ? $message['placeholder'] : '',
                                    'description'   => isset($message['description']) ? $message['description'] : '',
                                    'desc_tip'      => isset($message['desc_tip']) ? $message['desc_tip'] : false,
                                    'type'          => isset( $message['type'] ) ? $message['type'] : 'text',
                                    'data_type'     => isset( $message['data_type'] ) ? $message['data_type'] : '',
                                )
                            );
                            ?>
                            <li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div id="ouqw_import_export" class="ouqw_tabcontent" style="display: none;">
                <div class="options_group">
                    <div class="ouqw_group_option">
                        <img src="<?php echo esc_url(OUQW_PLUGIN_URL.'/assets/images/download-solid.svg') ?>" width="50" alt="">
                        <div>
                            <h3><?php esc_html_e('Export Settings', 'opal-upsale-quantity-for-woocommerce') ?></h3>
                            <p><?php esc_html_e('Download a backup file of your settings', 'opal-upsale-quantity-for-woocommerce') ?></p>
                        </div>
                    </div>
                    <div class="ouqw_action_button">
                        <a href="<?php echo esc_url(admin_url( 'admin-ajax.php' ).'?action=ouqw_settings_export&ajax_nonce_parameter='.wp_create_nonce( "ouqw-nonce-ajax" )); ?>" id="ouqw_download_settings" class="button button-primary"><?php esc_html_e('Download settings', 'opal-upsale-quantity-for-woocommerce') ?></a>
                    </div>
                </div>
                <form id="ouqw-form-import-settings" class="options_group" method="post" action="<?php echo esc_url(admin_url( 'admin-ajax.php' )) ?>" enctype="multipart/form-data">
                    <div class="ouqw_group_option">
                        <img src="<?php echo esc_url(OUQW_PLUGIN_URL.'/assets/images/file-import-solid.svg') ?>" width="50" alt="">
                        <div>
                            <h3><?php esc_html_e('Import Settings', 'opal-upsale-quantity-for-woocommerce') ?></h3>
                            <fieldset id="ouqw-import-form-settings">
                                <input type="hidden" name="action" value="ouqw_handle_import_settings">
                                <?php wp_nonce_field('ouqw-nonce-ajax', 'ajax_nonce_parameter');  ?>
                                <div class="ouqw_field_wrap">
                                    <input type="file" name="ouqw_setting_import" accept=".json,application/json" required="">
                                </div>
                                <p class="ouqw_notice"><?php esc_html_e('*Notice: All existing settings will be overwritten', 'opal-upsale-quantity-for-woocommerce') ?></p>
                            </fieldset>
                        </div>
                    </div>
                    <div class="ouqw_action_button">
                        <button id="ouqw_import_settings" class="button button-primary"><?php esc_html_e('Upload file and import settings', 'opal-upsale-quantity-for-woocommerce') ?></a>
                    </div>
                </form>
            </div>
        </div>
        <div class="ouqw_setting_action mt">
            <input type="hidden" name="action" value="ouqw_handle_settings_form">
            <?php wp_nonce_field('ouqw-nonce-ajax', 'ajax_nonce_parameter');  ?>
            <button type="button" id="ouqw_submit_settings" class="button"><?php esc_html_e('Save settings', 'opal-upsale-quantity-for-woocommerce') ?></button>
        </div>
    </div>
    <div style="clear: both"></div>
</div>
