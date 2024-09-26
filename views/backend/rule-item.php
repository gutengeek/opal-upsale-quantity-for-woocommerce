<?php
/** 
 * OUQW Rule Item Block
 * 
 * @uses rule_item
 * @uses index
 * @uses date_format
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly    

$rule_range_number = !empty($rule_item['rule_range_number']) ? $rule_item['rule_range_number'] : '';
$rule_discount_percent = !empty($rule_item['rule_discount_percent']) ? $rule_item['rule_discount_percent'] : '';

?>

<ul class="ouqw_rules_box ouqw-flex">
    <li class="option_item ouqw_group_settings_mt">
    <?php
        woocommerce_wp_text_input(
            array(
                'id'          => 'rule_range_number_'.$index,
                'value'       => $rule_range_number,
                'label'       => __( 'Number Items:', 'opal-upsale-quantity-for-woocommerce' ),
                'wrapper_class' => 'ouqw_setting_form', 
                'class' => 'ouqw_setting_field ouqw_rule_range_number',
                'style' => 'width:100%;margin-left:0',
                'type' => 'number',
                'custom_attributes' => [
                    'data-pattern-name' => 'rule_range_number_++',
                    'data-pattern-id' => 'rule_range_number_++',
                    'min' => '0'
                ]
            )
        );
    ?>
    </li>
    <li class="option_item ouqw_group_settings_mt">
    <?php
        woocommerce_wp_text_input(
            array(
                'id'          => 'rule_discount_percent_'.$index,
                'value'       => $rule_discount_percent,
                'label'       => __( 'Discount Percent: (%)', 'opal-upsale-quantity-for-woocommerce' ),
                'wrapper_class' => 'ouqw_setting_form', 
                'class' => 'ouqw_setting_field ouqw_input_float',
                'style' => 'width:100%;margin-left:0',
                'type' => 'text',
                'custom_attributes' => [
                    'data-pattern-name' => 'rule_discount_percent_++',
                    'data-pattern-id' => 'rule_discount_percent_++',
                    'data-decimal' => '2',
                ]
            )
        );
    ?>
    </li>
    <div class="rule_action_btn repeater_btn"><a href="javascript:void(0)" class="rpt_btn_remove"><i class="dashicons dashicons-no-alt"></i></a></div>
</ul>