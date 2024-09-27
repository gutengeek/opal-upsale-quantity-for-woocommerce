<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'OUQW_Frontend' ) ) :

	/**
	 * Main OUQW_Frontend Class.
	 *
	 * @package		OUQW
	 * @subpackage	Classes/OUQW_Frontend
	 * @since		1.0.0
	 * @author		Opal
	 */
	final class OUQW_Frontend {

        /**
		 * OUQW settings object.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
		private $settings;
        
        /**
		 * OUQW settings_data.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
		private $settings_data;

        protected static $calculated = false;

        public function __construct() {
            // Settings object
			$this->settings = ouqw()->settings;
            $this->settings_data = $this->settings->ouqw_get_settings_data();
            
            // Run in frontend
            $this->ouqw_add_filter();
            $this->ouqw_add_action();

            // Add shortcode
            add_shortcode( 'ouqw', [ $this, 'ouqw_shortcode' ] );
        }

        /**
		 *  Call View Fontend Template
		 */
		public static function view($view, $data = array()) {
			extract($data);
			$path_view = apply_filters('ouqw_path_view_fontend', OUQW_PLUGIN_DIR . 'views/frontend/' . $view . '.php', $data);
			include($path_view);
		}
        
        /**
		 * OUQW add action hook.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
        private function ouqw_add_action() {

            $render_hook = $this->handle_render_hook_product();
            if ($render_hook) {
                add_action( $render_hook['hook_action'], [$this, 'render_in_product_page'], absint($render_hook['prioty']) ); 
            }

            add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 9999 );
			add_action( 'woocommerce_before_mini_cart_contents', [ $this, 'mini_cart_contents' ], 9999 );
			add_action( 'woocommerce_widget_shopping_cart_total', [ $this, 'hidden_cart_total' ], 9999 );

            add_action( 'woocommerce_checkout_create_order_line_item', [$this, 'create_order_line_item_meta'], 10, 4 );
            add_action( 'woocommerce_new_order_item', [$this, 'custom_display_order_item_meta'], 10, 3 );
            
            if (ouqw_get_option('custom_quantity_input', false, $this->settings_data)) {
                add_action( 'woocommerce_before_add_to_cart_quantity', [$this, 'before_custom_qty'], 99 );
                add_action( 'woocommerce_before_add_to_cart_quantity', [$this, 'custom_qty'], 100 );
                add_action( 'woocommerce_after_add_to_cart_quantity', [$this, 'after_custom_qty'], 1 );
            }
            
            // add_action( 'woocommerce_after_add_to_cart_quantity', [$this, 'after_custom_qty'], 1 );
        }
        
        /**
		 * OUQW add filter hook.
		 *
		 * @access	private
		 * @since	1.0.0
		 */
        private function ouqw_add_filter() {
            add_filter( 'woocommerce_cart_item_price', [ $this, 'cart_item_price' ], 10, 3 );
            add_filter( 'woocommerce_get_item_data', [$this, 'display_cart_item_data'], 10, 2 );
            add_filter( 'woocommerce_product_price_class', [$this, 'add_price_class'], 9999 );
            add_filter( 'woocommerce_quantity_input_classes', [$this, 'add_qty_input_class'], 9999, 2 );
            add_filter( 'woocommerce_format_sale_price', [$this, 'format_sale_price'], 9999, 3 );
        }

        /**
         * OUQW add shortcode
         *
         * @access  public
         * @since   1.0.0
         */
        public function ouqw_shortcode($args) {
            if(isset($args['id'])) {
                $product_id = $args['id'];
            }
            elseif (is_product()) {
                $product_id = get_the_ID();
            }
            else {
                echo esc_html__('Please add a product id into shortcode', 'opal-upsale-quantity-for-woocommerce');
                return;
            }

            if (!$this->is_enable($product_id)) return;

            $range_data = ouqw_get_option('rules_range', false, $this->settings_data);
            $range_data = self::check_range_data($range_data);
            if (!$range_data) {
                return;
            }
            
            $product_render_type = ouqw_get_option('product_render_type', 'badge', $this->settings_data);
            $discount_type = ouqw_get_option('discount_type', 'product_items', $this->settings_data);

            switch ($discount_type) {
                case 'cart_items':
                    $discount_type_text = ouqw_get_option('discount_by_cart_items', '', $this->settings_data);
                    break;
                default:
                    $discount_type_text = ouqw_get_option('discount_by_product_items', '', $this->settings_data);
                    break;
            }

            if (is_product() && !$this->handle_render_hook_product()) {
                wp_localize_script( 'ouqw-frontend-scripts', 'ouqw_tiers', apply_filters( 'ouqw_tier_var_script', [
                    'range_data' => $range_data,
                    'discount_type' => $discount_type,
                    'product_render_type' => $product_render_type,
                ], $this->settings_data ) );
            }

            $product = wc_get_product($product_id);
            if ( $product->is_type( 'variable' ) ) {
                $product = new WC_Product_Variable($product_id);
                $raw_price = $product->get_variation_price();
            }   
            else {
                $raw_price = $product->get_price();
            }


            /**
             * Functions hooked in to ouqw_before_upsale_discount_shortcode action
             *
             * @see ouqw_wrapper_open_upsale_view - 10
             *
             */
            do_action('ouqw_before_upsale_discount_shortcode', $this->settings_data);

            self::view($product_render_type, [
                'raw_price' => $raw_price,
                'range_data' => $range_data,
                'discount_type_text' => $discount_type_text,
                'product_type' => $product->get_type(),
                'show' => true
            ]);

            /**
             * Functions hooked in to ouqw_after_upsale_discount_shortcode action
             *
             * @see ouqw_wrapper_close_upsale_view - 10
             *
             */
            do_action('ouqw_after_upsale_discount_shortcode', $this->settings_data);

        }

        public function render_in_product_page() {
            global $product;
            if (!$this->is_enable($product->get_id())) return;

            $range_data = ouqw_get_option('rules_range', false, $this->settings_data);
            $range_data = self::check_range_data($range_data);
            if (!$range_data) {
                return;
            }
            
            $product_render_type = ouqw_get_option('product_render_type', 'tier-line', $this->settings_data);
            $show_badge = ouqw_get_option('show_badge', false, $this->settings_data);
            $discount_type = ouqw_get_option('discount_type', 'product_items', $this->settings_data);

            switch ($discount_type) {
                case 'cart_items':
                    $discount_type_text = ouqw_get_option('discount_by_cart_items', '', $this->settings_data);
                    break;
                default:
                    $discount_type_text = ouqw_get_option('discount_by_product_items', '', $this->settings_data);
                    break;
            }

            wp_localize_script( 'ouqw-frontend-scripts', 'ouqw_tiers', apply_filters( 'ouqw_tier_var_script', [
                'range_data' => $range_data,
                'discount_type' => $discount_type,
                'product_render_type' => $product_render_type,
            ], $this->settings_data ) );

            /**
             * Functions hooked in to ouqw_before_upsale_discount_view action
             *
             * @see ouqw_wrapper_open_upsale_view - 10
             *
             */
            do_action('ouqw_before_upsale_discount_view', $this->settings_data);

            if ($show_badge) {
                self::view('badge', [
                    'badge_title' => ouqw_get_option('badge_title', '', $this->settings_data),
                    'badge_text' => ouqw_get_option('badge_text', '', $this->settings_data),
                ]);
            }

            if ( $product->is_type( 'variable' ) ) {
                $raw_price = $product->get_variation_price();
            }   
            else {
                $raw_price = $product->get_price();
            }

            self::view($product_render_type, [
                'raw_price' => $raw_price,
                'range_data' => $range_data,
                'discount_type_text' => $discount_type_text,
                'product_type' => $product->get_type(),
                'show' => true
            ]);

            /**
             * Functions hooked in to ouqw_after_upsale_discount_view action
             *
             * @see ouqw_wrapper_close_upsale_view - 10
             *
             */
            do_action('ouqw_after_upsale_discount_view', $this->settings_data);
        }

        private function handle_render_hook_product() {
            $settings_data = $this->settings_data;
            $render_hook = ouqw_get_option('product_render_position', '', $settings_data);

            if (empty($render_hook)) return false;

            $options = explode( '-', $render_hook ) ;
            $hook_action = isset($options[0]) ? $options[0] : 'woocommerce_before_add_to_cart_button';
            $prioty = isset($options[1]) ? $options[1] : 100;

            $render_prioty = ouqw_get_option('render_position_prioty', false, $settings_data);
            if ($render_prioty && $render_prioty != '') {
                $prioty = $render_prioty;
            }

            return [
                'hook_action' => $hook_action,
                'prioty' => $prioty
            ];
        }

        public function before_calculate_totals( $cart ) {
			if ( ! self::$calculated ) {
				if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {
					// This is necessary for WC 3.0+
					return;
				}           

                $range_data = ouqw_get_option('rules_range', false, $this->settings_data);
                $discount_type = ouqw_get_option('discount_type', 'product_items', $this->settings_data);

				if ( ! empty( $cart->cart_contents ) ) {
					foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
						$product_id = $cart_item['data']->get_id();
                        $discount = false;

						if ( $this->is_enable( $product_id ) ) {
							$ori_product = apply_filters( 'ouqw_get_ori_product', wc_get_product( $product_id ), $cart_item, $cart_item_key );
							$ori_price   = apply_filters( 'ouqw_get_ori_product_price', $ori_product->get_price(), $cart_item, $cart_item_key );

                            $qty = $cart_item['quantity'];
                            // Check discount type
                            if ($discount_type && $discount_type == 'cart_items') {
                                $qty = WC()->cart->get_cart_contents_count();
                            }

                            $discount = floatval(self::get_discount_by_range($range_data, $qty));
                            if ($discount && $discount > 0) {
                                $new_price = self::get_price_by_range($ori_price, $discount);
                                $new_price = round( $new_price, wc_get_price_decimals() );
    
                                $cart_item['data']->set_price( $new_price );
    
                                if ( empty( $cart_item['ouqw_qty'] ) || ( $cart_item['quantity'] != $cart_item['ouqw_qty'] ) ) {
                                    // store price at current quantity
                                    WC()->cart->cart_contents[ $cart_item_key ]['ouqw_qty']       = $cart_item['quantity'];
                                    WC()->cart->cart_contents[ $cart_item_key ]['ouqw_discount']       = $discount;
                                    WC()->cart->cart_contents[ $cart_item_key ]['ouqw_ori_price'] = $ori_price;
                                    WC()->cart->cart_contents[ $cart_item_key ]['ouqw_price']     = $new_price;
                                }
                            }
                            else {
                                unset(WC()->cart->cart_contents[ $cart_item_key ]['ouqw_qty']);
                                unset(WC()->cart->cart_contents[ $cart_item_key ]['ouqw_discount']);
                                unset(WC()->cart->cart_contents[ $cart_item_key ]['ouqw_ori_price']);
                                unset(WC()->cart->cart_contents[ $cart_item_key ]['ouqw_price']);
                            }
						}
					}
				}

                // die();
				self::$calculated = true;
			}
		}

        public function is_enable( $product_id, $test = false ) {
            $product = wc_get_product($product_id);

            if ($product->is_sold_individually() && !$product->is_type('grouped')) {
                return apply_filters( 'ouqw_disable_product_individually', false, $product_id );
            }

			$is_enable = false;
            $stock_status = $product->get_stock_status();
            $rule_apply_for = ouqw_get_option('rule_apply_for', false, $this->settings_data);
        
            if ($rule_apply_for) {
                $apply_select = ouqw_get_option('rule_apply_select_val', [], $this->settings_data);
                switch ($rule_apply_for) {
                    case 'all':
                        $is_enable = true;
                        break;
                    case 'instock':
                        $is_enable = $stock_status == 'instock';
                        break;
                    case 'outofstock':
                        $is_enable = $stock_status == 'outofstock';
                        break;
                    case 'onbackorder':
                        $is_enable = $stock_status == 'onbackorder';
                        break;
                    case 'product':
                        if (is_array($apply_select) && in_array($product_id, $apply_select)) {
                            $is_enable = true;
                        }
                        break;
                    case 'category':
                        if (ouqw_is_product_in_taxs($product_id, $apply_select, 'product_cat')) {
                            $is_enable = true;
                        }
                        break;
                    case 'tag':
                        if (ouqw_is_product_in_taxs($product_id, $apply_select, 'product_tag')) {
                            $is_enable = true;
                        }
                        break;
                    default:
                        if (is_array($apply_select) && in_array($product->get_type(), $apply_select)) {
                            $is_enable = true;
                        }
                        break;
                }
            }

			return apply_filters( 'ouqw_product_is_enable', $is_enable, $product_id );
		}

        private static function check_range_data($range_data) {
            if (!$range_data || empty($range_data) || !is_array($range_data )) {
                return false;
            }

            $valid = false;
            foreach ($range_data as $tier) {
                if (empty($tier['rule_range_number']) || empty($tier['rule_discount_percent'])) {
                    continue;
                }
                $valid = true;
            }
            
            if ($valid) {
                usort($range_data, function ($a, $b) {
                    return $a["rule_range_number"] <=> $b["rule_range_number"];
                });
                return $range_data;
            } 
            else {
                return false;
            }
            
        }

        public static function get_discount_by_range($range_data, $qty) {
            $range_data = self::check_range_data($range_data);
            if (!$range_data) {
                return false;
            }

            $discount = 0;
            foreach ($range_data as $tier) {
                if (empty($tier['rule_range_number']) || empty($tier['rule_discount_percent'])) {
                    continue;
                }

                if ($qty >= $tier['rule_range_number']) {
                    $discount = floatval($tier['rule_discount_percent']);
                }
            }

            return apply_filters( 'ouqw_product_discount_by_range', $discount, $range_data );
        }

        public static function get_price_by_range($ori_price, $discount) {
            if ($discount > 0) {
                $discount = $discount >= 100 ? 99 : $discount;
                $new_price = $ori_price - ($ori_price * $discount / 100);
                return $new_price;
            }
            else {
                return $ori_price;
            }
        }


		public function cart_item_price( $price, $cart_item, $cart_item_key ) {
			$ori_product = apply_filters( 'ouqw_get_ori_product', wc_get_product( $cart_item['data']->get_id() ), $cart_item, $cart_item_key );
			$ori_price   = apply_filters( 'ouqw_get_ori_product_price', $ori_product->get_price(), $cart_item, $cart_item_key );
			$new_price   = $cart_item['data']->get_price();

			if ( (float) $ori_price !== (float) $new_price ) {
				return wc_format_sale_price( $ori_price, $new_price );
			}

			return $price;
		}

		public function mini_cart_contents() {
			WC()->cart->calculate_totals();
		}

		public function hidden_cart_total() {
            $cart_count = absint( WC()->cart->get_cart_contents_count() );
			?>
            <input type="hidden" id="ouqw_cart_count" value="<?php echo esc_attr($cart_count) ?>">
            <?php
		}

        public static function get_discount_var($discount_number, $qty, $settings_data) {
            $discount_type = ouqw_get_option('discount_type', 'product_items', $settings_data);
            $discount_text = ouqw_get_option('discount_by_product_items', '', $settings_data);
            if ($discount_type && $discount_type == 'cart_items') {
                $discount_text = ouqw_get_option('discount_by_cart_items', '', $settings_data);
            }

            return [
                'name' => '<strong>'.ouqw_get_option('badge_title', '', $settings_data).'</strong>',
                // translators: %1$s: Quantity.
                // translators: %2$s: Discount number (percent).
                // translators: %3$s: Discount text.
                'value' => sprintf(__('%1$s items get <strong>%2$s%% OFF</strong> %3$s', 'opal-upsale-quantity-for-woocommerce'), $qty, $discount_number, $discount_text)
            ];
        }

        public function display_cart_item_data($cart_data, $cart_item) {
            if (!ouqw_get_option('show_in_cart_item', false, $this->settings_data)) {
                return $cart_data;
            }

            if (!empty($cart_item['ouqw_discount'])) {
                $cart_data[$cart_item['key']] = self::get_discount_var($cart_item['ouqw_discount'], $cart_item['quantity'], $this->settings_data);
            }

            return $cart_data;
        }

        public function add_price_class($class) {
            return $class.' ouqw_price_box';
        }

        public function add_qty_input_class($classes, $product) {
            if (!is_array($classes)) {
                $classes = [];
            }
            $classes[] = 'ouqw-qty-input';
            return $classes;
        }

        public function format_sale_price( $price, $regular_price, $sale_price ) {
            // Format the prices.
            $formatted_regular_price = is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price;
            $formatted_sale_price    = is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price;
        
            // Strikethrough pricing.
            $price = '<del aria-hidden="true">' . $formatted_regular_price . '</del> ';
        
            // For accessibility (a11y) we'll also display that information to screen readers.
            $price .= '<span class="screen-reader-text">';
            // translators: %s is a product's regular price.
            $price .= esc_html( sprintf( __( 'Original price was: %s.', 'opal-upsale-quantity-for-woocommerce' ), wp_strip_all_tags( $formatted_regular_price ) ) );
            $price .= '</span>';
        
            // Add the sale price.
            $price .= '<ins class="sale-price" aria-hidden="true">' . $formatted_sale_price . '</ins>';
        
            // For accessibility (a11y) we'll also display that information to screen readers.
            $price .= '<span class="screen-reader-text">';
            // translators: %s is a product's current (sale) price.
            $price .= esc_html( sprintf( __( 'Current price is: %s.', 'opal-upsale-quantity-for-woocommerce' ), wp_strip_all_tags( $formatted_sale_price ) ) );
            $price .= '</span>';
        
            return apply_filters( 'ouqw_woocommerce_format_sale_price', $price, $regular_price, $sale_price );
        }

        /**
         *  Add Value of Custom Field to Order Item Meta
         */
        public function create_order_line_item_meta( $item, $cart_item_key, $values, $order ) {
            if (isset($values['product_id'])) {
                $product_id = $values['product_id'];
                if ($this->is_enable($product_id) && !empty($values['ouqw_discount'])) {    

                    $discount_var = self::get_discount_var($values['ouqw_discount'], $values['quantity'], $this->settings_data);

                    $item->add_meta_data('ouqw_discount', $discount_var, true);

                }
            }
        }
        
        public function custom_display_order_item_meta($item_id, $item, $order_id) {
            if($item instanceof WC_Order_Item_Product) {
                $product_id = $item->get_product_id();
                $ouqw_discount = $item->get_meta('ouqw_discount');

                if (!empty($ouqw_discount) && $ouqw_discount) {
                    wc_update_order_item_meta($item_id, $ouqw_discount['name'], $ouqw_discount['value']);
                }
            }
        }

        public function before_custom_qty() {
            global $product;
            if (!$this->is_enable($product->get_id())) return;
            $range_data = ouqw_get_option('rules_range', false, $this->settings_data);
            $range_data = self::check_range_data($range_data);
            if (!$range_data) {
                return;
            }
            ?>
            <div class="ouqw_wraper_qty">
            <?php
        }
   
        public function after_custom_qty() {
            global $product;
            if (!$this->is_enable($product->get_id())) return;
            $range_data = ouqw_get_option('rules_range', false, $this->settings_data);
            $range_data = self::check_range_data($range_data);
            if (!$range_data) {
                return;
            }

            $custom = __('Custom quantities', 'opal-upsale-quantity-for-woocommerce');
            $standard = __('Standard quantities', 'opal-upsale-quantity-for-woocommerce');
            ?>
                <span class="show_qty_input" data-text_back="<?php echo esc_attr($custom) ?>"><?php echo esc_html($standard) ?></span>
            </div>
            <?php
        }

        public function custom_qty() {
            global $product;
            if (!$this->is_enable($product->get_id())) return;

            $range_data = ouqw_get_option('rules_range', false, $this->settings_data);
            $range_data = self::check_range_data($range_data);
            if (!$range_data) {
                return;
            }
            
            $product_render_type = ouqw_get_option('product_render_type', 'tier-line', $this->settings_data);
            $discount_type = ouqw_get_option('discount_type', 'product_items', $this->settings_data);

            switch ($discount_type) {
                case 'cart_items':
                    $discount_type_text = ouqw_get_option('discount_by_cart_items', '', $this->settings_data);
                    break;
                default:
                    $discount_type_text = ouqw_get_option('discount_by_product_items', '', $this->settings_data);
                    break;
            }

            if (is_product() && !$this->handle_render_hook_product()) {
                wp_localize_script( 'ouqw-frontend-scripts', 'ouqw_tiers', apply_filters( 'ouqw_tier_var_script', [
                    'range_data' => $range_data,
                    'discount_type' => $discount_type,
                    'product_render_type' => $product_render_type,
                ], $this->settings_data ) );
            }

            do_action('ouqw_before_custom_qty', $this->settings_data);

            if ( $product->is_type( 'variable' ) ) {
                $raw_price = $product->get_variation_price();
            }   
            else {
                $raw_price = $product->get_price();
            }

            self::view('custom_qty', [
                'raw_price' => $raw_price,
                'range_data' => $range_data,
                'discount_type_text' => $discount_type_text,
                'product_type' => $product->get_type(),
            ]);

            do_action('ouqw_before_custom_qty', $this->settings_data);

        }
    }
endif;