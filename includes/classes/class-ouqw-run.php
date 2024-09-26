<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class OUQW_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		OUQW
 * @subpackage	Classes/OUQW_Run
 * @author		Opal
 * @since		1.0.0
 */
class OUQW_Run{

	/**
	 * Our OUQW_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 20 );		
	
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		global $post_type_object, $typenow, $pagenow, $current_screen;

		wp_register_script( 'ouqw-form-repeater-lib', OUQW_PLUGIN_URL . 'assets/js/libs/form-repeater.js', array( 'jquery' ), OUQW_VERSION, true );
		wp_register_script( 'ouqw-input-number-format-lib', OUQW_PLUGIN_URL . 'assets/js/libs/input-number-format.jquery.min.js', array( 'jquery' ), OUQW_VERSION, true );
		wp_register_script( 'ouqw-toast-notice-script', OUQW_PLUGIN_URL . 'assets/js/libs/jquery.toast.min.js', array( 'jquery' ), OUQW_VERSION, true );
		wp_register_script( 'ouqw-backend-scripts', OUQW_PLUGIN_URL . 'assets/js/backend/backend-scripts.js', array( 'jquery' ), OUQW_VERSION, true );

		wp_register_style( 'ouqw-backend-styles', OUQW_PLUGIN_URL . 'assets/css/backend-styles.css', array(), OUQW_VERSION, 'all' );
		wp_register_style( 'ouqw-toast-notice-style', OUQW_PLUGIN_URL . 'assets/css/libs/jquery.toast.min.css', array(), OUQW_VERSION, 'all' );

		wp_localize_script( 'ouqw-backend-scripts', 'ouqw_script', array(
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "ouqw-nonce-ajax" )
		));

		wp_enqueue_style( 'ouqw-backend-styles' );
		wp_enqueue_style( 'ouqw-toast-notice-style' );
		
		wp_enqueue_script( 'ouqw-form-repeater-lib' );
		wp_enqueue_script( 'ouqw-input-number-format-lib' );
		wp_enqueue_script( 'ouqw-toast-notice-script' );
		wp_enqueue_script( 'ouqw-backend-scripts' );

		// wp_enqueue_media();
		// wp_enqueue_script('wp-color-picker');
    	// wp_enqueue_style('wp-color-picker');
	}

	
	/**
	 * Enqueue the frontend related scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_frontend_scripts_and_styles() {
		wp_register_script( 'ouqw-frontend-scripts', OUQW_PLUGIN_URL . 'assets/js/frontend/frontend-scripts.js', array( 'jquery', 'accounting' ), OUQW_VERSION, true );
		wp_localize_script( 'ouqw-frontend-scripts', 'ouqw_script', array(
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "ouqw-nonce-ajax" )
		));

		wp_register_style( 'ouqw-frontend-styles', OUQW_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), OUQW_VERSION, 'all' );
		wp_enqueue_style( 'ouqw-frontend-styles' );

		wp_enqueue_script( 'ouqw-frontend-scripts' );
		wp_localize_script( 'ouqw-frontend-scripts', 'ouqw_wc_vars', [
			'currency_format_num_decimals' => wc_get_price_decimals(),
			'currency_format_symbol'       => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
			'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
			'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
			'cart_count'              	   => WC()->cart->get_cart_contents_count(),
		] );

		if (is_singular('product')) {
			$this->localize_product_single();
		}
	}

	private function localize_product_single(){
		$product = wc_get_product(get_the_ID());
		$stock_status = $product->get_stock_status();
		$stock_quantity = $product->get_stock_quantity();
		
		$product_data = array(
			'stock_status'	=> $stock_status,
			'stock_quantity'	=> $stock_quantity,
			'product_id' => get_the_ID(),
			'type' => $product->get_type(),
			'product_price' => $product->get_price()
		);

		if ( $product->is_type( 'variable' ) ) {
			$product = new WC_Product_Variable(get_the_ID());
			$product_variations = $product->get_available_variations();
			$stock_quantity = [];
			
			foreach ($product_variations as $variation)  {
				$variation_id = $variation['variation_id'];
				$variation_obj = new WC_Product_Variation( $variation_id );
				$stock_status = $variation_obj->get_stock_status();
				$stock_qty = $variation_obj->get_stock_quantity();
				// $variation['attributes']['variation_id'] = $variation['variation_id'];

				$var_data = $variation['attributes'];
				$var_data['stock_status'] = $stock_status;
				$var_data['stock_quantity'] = $stock_qty;
				$var_data['display_price'] = $variation['display_price'];

				$product_data['variation'][$variation['variation_id']] = $var_data;
			}
		}
		elseif ( $product->is_type('grouped') ) {
			$products = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );
			if (!empty($products)) {
				foreach ($products as $child_prod) {
					$var_data = [];
					$var_data['display_price'] = $child_prod->get_price();
					$product_data['grouped'][$child_prod->get_id()] = $var_data;
				}
			}
		}
		
		wp_localize_script( 'ouqw-frontend-scripts', 'ouqw_product', $product_data );
	}
}
