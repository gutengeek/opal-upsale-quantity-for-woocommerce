<?php
use Automattic\WooCommerce\Admin\Features\Features;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class OUQW_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		OUQW
 * @subpackage	Classes/OUQW_Settings
 * @author		Opal
 * @since		1.0.0
 */
class OUQW_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * Our OUQW_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->plugin_name = OUQW_NAME;
		$plugin = OUQW_PLUGIN_BASE;
		
        add_filter("plugin_action_links_$plugin", array($this, 'add_settings_link'));

		register_activation_hook(OUQW_PLUGIN_FILE, array($this, 'install'));
		register_activation_hook(OUQW_PLUGIN_FILE, array($this, 'ouqw_deactive_without_woocommerce'));
		register_deactivation_hook(OUQW_PLUGIN_FILE, array($this, 'deactivation'));

		add_action( 'admin_menu', [$this, 'ouqw_custom_submenu' ] );

		add_action( 'wp_ajax_ouqw_load_rule_apply_ajax', [$this, 'ouqw_load_rule_apply_ajax'] ); // wp_ajax_{action}
		add_action( 'wp_ajax_ouqw_handle_settings_form', [$this, 'ouqw_handle_settings_form'] );
		add_action( 'wp_ajax_ouqw_settings_export', [$this, 'ouqw_settings_export'] );
		add_action( 'wp_ajax_ouqw_handle_import_settings', [$this, 'ouqw_handle_import_settings'] );

		// add_action(OUQW_CRON_HOOK, array($this, 'ouqw_delete_temp_files'));
		add_action('admin_init', array($this, 'ouqw_trigger_deactice_addon_without_woocommerce'));
	}

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'OUQW/settings/get_plugin_name', $this->plugin_name );
	}

	public function add_settings_link($links) {
		if ( !ouqw_check_woocommerce_active() ) return $links;

        $settings = '<a href="' . admin_url('admin.php?page=ouqw-settings') . '">' . esc_html__('Settings', 'opal-upsale-quantity-for-woocommerce') . '</a>';
        array_push($links, $settings);
        
        return $links;
    }

	public function ouqw_deactive_without_woocommerce() {
		if (!class_exists('Woocommerce')) {
			add_action( 'admin_notices', array($this, 'ouqw_child_plugin_notice') );
			// deactivate_plugins(OUQW_PLUGIN_BASE);
		}
	}
	
	public function ouqw_trigger_deactice_addon_without_woocommerce() {
		if (!class_exists('Woocommerce')) {
			add_action( 'admin_notices', array($this, 'ouqw_child_plugin_notice') );
		}
	}
	
	public function ouqw_child_plugin_notice(){
		$message = __('<strong>Opal Upsale Quantity for Woocommerce</strong> is an addon extention of <strong>Woocommerce Plugin</strong>. Please active <strong>Woocommerce Plugin</strong> to be able to use this extention!', 'opal-upsale-quantity-for-woocommerce');
		?>
		<div class="error"><p><?php echo wp_kses_post($message); ?></p></div>
		<?php
	}

	public function install() {
		$this->ouqw_add_default_settings();
	}

	public function deactivation() {
		wp_clear_scheduled_hook(OUQW_CRON_HOOK);
	}

	/**
	 *  Call View Admin Template
	 */
	public static function view($view, $data = array()) {
		extract($data);
		$path_view = apply_filters('ouqw_path_view_admin', OUQW_PLUGIN_DIR . 'views/backend/' . $view . '.php', $data);
		include($path_view);
	}

	private function ouqw_add_default_settings() {
		$settings_option = get_option(OUQW_SETTINGS_KEY);
		if (!$settings_option) {
			$settings = $this->ouqw_get_settings_default();
			update_option(OUQW_SETTINGS_KEY, wp_json_encode($settings));
		}
	}

	public function ouqw_get_settings_default() {
		$settings = [
			'product_render_position' => 'woocommerce_single_product_summary-30',
			'render_position_prioty' => '',
			'product_render_type' => 'tier-line',
			'show_badge' => 1,
			'custom_quantity_input' => 1,
			'show_in_cart_item' => 1,
			'show_in_cart_total' => 0,
			'show_in_order' => 1,
			'rule_apply_for' => 'all', 
			'rule_apply_select_val' => '', 
			'discount_type' => 'product_items',
			'rules_range' => [
				[
					'rule_range_number' => '',
					'rule_discount_percent' => '',
				]
			]
		];

		require OUQW_PLUGIN_DIR.'includes/helpers/define.php';
		foreach ($validates_message as $name => $message) {
			$settings[$name] = $message['value'];
		}

		return $settings;
	}

	public function ouqw_get_settings_data() {
		$settings = get_option(OUQW_SETTINGS_KEY, wp_json_encode($this->ouqw_get_settings_default()));
		return $settings;
	}

	public function prepare_template_export() {
		$settings = $this->ouqw_get_settings_data();
		
		$file_data = [
			'name' => 'ouqw-data-settings-' . gmdate( 'Y-m-d' ) . '.json',
			'content' =>  $settings,
		];

		return $file_data;
	}

	public function ouqw_custom_submenu() {
		global $pagenow;

		add_submenu_page(
			'woocommerce',
			__( 'OUQW Setting', 'opal-upsale-quantity-for-woocommerce' ),
			__( 'Upsale Quantity Price', 'opal-upsale-quantity-for-woocommerce' ),
			'manage_options',
			'ouqw-settings',
			[$this, 'ouqw_setting_page_callback'],
		);

		if (isset($_GET['page']) && $_GET['page'] == 'ouqw-settings') {
			remove_all_actions( 'admin_notices' );
		}
	}
	
	public function ouqw_setting_page_callback() {
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'wc-admin-layout' );
		wp_enqueue_script( 'woocommerce_admin' );
		wp_enqueue_script( 'jquery-tiptip' );
		
		$settings_data = $this->ouqw_get_settings_data();
		self::view('admin-settings', ['settings' => $settings_data]);
	}	

	public function ouqw_load_rule_apply_ajax(){
		check_ajax_referer( 'ouqw-nonce-ajax', 'ajax_nonce_parameter' );

		if(empty($_GET['q'])) return false;
		if(empty($_GET['term'])) return false;

		$kw = sanitize_text_field(wp_unslash($_GET['q']));
		$term = sanitize_text_field(wp_unslash($_GET['term']));

		$func_search = 'ouqw_get_'.$term.'_by_keyword';

		$return = $this->$func_search($kw);

		if (!$return) return false;
		echo wp_json_encode( $return );
		die;
	}

	private function ouqw_get_product_by_keyword($kw) {
		$return = false;

		$search_results = new WP_Query( array( 
			's'=> wc_clean($kw), // the search query
			'post_status' => 'publish', // if you don't want drafts to be returned
			'post_type' => 'product',
			'posts_per_page' => -1 // how much to show at once
		) );

		if( $search_results->have_posts() ) {
			$return = [];
			while( $search_results->have_posts() ) : $search_results->the_post();	
				// shorten the title a little
				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
				$return[] = array( $search_results->post->ID, $title );
			endwhile;
		}
		
		return $return;
	}

	private function ouqw_get_category_by_keyword($kw) {
		global $wpdb;
		$taxonomy = 'product_cat';
		$return = false;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT t.*, tt.*
				FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				WHERE tt.taxonomy = %s
				AND t.name LIKE %s",
				$taxonomy,
				'%' . $wpdb->esc_like($kw) . '%'
			)
		);

		// In kết quả
		if ($results && !empty($results)) {
			$return = [];
			foreach ($results as $term) {
				// shorten the title a little
				$title = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
				$return[] = array( $term->term_id, $title );
			}
		}
		return $return;
	}

	private function ouqw_get_tag_by_keyword($kw) {
		global $wpdb;
		$taxonomy = 'product_tag';
		$return = false;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT t.*, tt.*
				FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				WHERE tt.taxonomy = %s
				AND t.name LIKE %s",
				$taxonomy,
				'%' . $wpdb->esc_like($kw) . '%'
			)
		);

		// In kết quả
		if ($results && !empty($results)) {
			$return = [];
			foreach ($results as $term) {
				// shorten the title a little
				$title = ( mb_strlen( $term->name ) > 50 ) ? mb_substr( $term->name, 0, 49 ) . '...' : $term->name;
				$return[] = array( $term->term_id, $title );
			}
		}
		return $return;
	}

	private function ouqw_get_type_by_keyword($kw) {
		$product_types = wc_get_product_types();
		$return = [];
		if ($product_types) {
			foreach ($product_types as $product_type => $label) {
				$return[] = array( $product_type, $label );
			}
		} 
		else {
			return false;
		}
		return $return;
	}

	private function ouqw_get_shipping_class_by_keyword($kw) {
		$shipping_classes = WC()->shipping->get_shipping_classes();
		$return = [];
		if ($shipping_classes) {
			foreach ($shipping_classes as $class) {
				$return[] = array( $class->term_id, $class->name );
			}
		} 
		else {
			return false;
		}
		return $return;
	}

	public function ouqw_handle_settings_form() {
		check_ajax_referer( 'ouqw-nonce-ajax', 'ajax_nonce_parameter' );

		$settings = $this->ouqw_get_settings_default();

		foreach ($settings as $name => $field) {
			if ($name != 'rules_range') {
				$field_val = isset($_POST[$name]) ? wc_clean(wp_unslash($_POST[$name])) : 0;
				$settings[$name] = $field_val;
			}
		}

		$rule_field = $settings['rules_range'][0];
		$rules_range = [];
		$i = 0;
		while (isset($_POST['rule_range_number_'.$i])) {
			$rules_range[$i] = [];
			foreach ($rule_field as $field => $default) {
				if (isset($_POST[$field.'_'.$i])) {
					$rules_range[$i][$field] = wc_clean(wp_unslash($_POST[$field.'_'.$i]));
				}
			}
			$i++;
		}
		$settings['rules_range'] = $rules_range;

		$flag = update_option(OUQW_SETTINGS_KEY, wp_json_encode($settings, JSON_UNESCAPED_UNICODE));
		update_option('ouqw_updated_settings', 1);

		wp_send_json_success( [
			'message' => esc_html__('Update settings successfully!', 'opal-upsale-quantity-for-woocommerce')
		] );
		
		die();
	}

	public function ouqw_settings_export() {
		check_ajax_referer( 'ouqw-nonce-ajax', 'ajax_nonce_parameter' );

		$file_data = $this->prepare_template_export();

		if ( is_wp_error( $file_data ) ) {
			return $file_data;
		}

		ouqw_send_file_headers( $file_data['name'], strlen( $file_data['content'] ) );

		// Clear buffering just in case.
		@ob_end_clean();

		flush();

		// Output file contents.
		add_filter('esc_html', 'ouqw_prevent_escape_html', 99, 2);
		echo esc_html($file_data['content']);
		remove_filter('esc_html', 'ouqw_prevent_escape_html', 99, 2);

		die;
	}

	public function ouqw_handle_import_settings() {
		check_ajax_referer( 'ouqw-nonce-ajax', 'ajax_nonce_parameter' );

		if (isset($_FILES['ouqw_setting_import']["error"]) && $_FILES['ouqw_setting_import']["error"] != 4) {
			if ($_FILES['ouqw_setting_import']["error"] == UPLOAD_ERR_INI_SIZE) {
				$error_message = esc_html__('The uploaded file exceeds the maximum upload limit', 'opal-upsale-quantity-for-woocommerce');
			} else if (in_array($_FILES['ouqw_setting_import']["error"], array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE))) {
				$error_message = esc_html__('The uploaded file exceeds the maximum upload limit', 'opal-upsale-quantity-for-woocommerce');
			}
			if (isset($_FILES['ouqw_setting_import']['name']) && isset($_FILES['ouqw_setting_import']['type'])) {
				$ext = pathinfo(sanitize_file_name($_FILES['ouqw_setting_import']['name']), PATHINFO_EXTENSION);
				if ($ext != 'json' || $_FILES['ouqw_setting_import']['type'] != 'application/json') {
					$error_message = esc_html__('Only allow upload Json(.json) file', 'opal-upsale-quantity-for-woocommerce');
				}
			}
		}
		else {
			$error_message = esc_html__('Please upload a file to import', 'opal-upsale-quantity-for-woocommerce');
		}
		
		$data_upload = '';
		if (isset($_FILES['ouqw_setting_import']['tmp_name'])) {
			$data_upload = file_get_contents(sanitize_url($_FILES['ouqw_setting_import']['tmp_name']));
			// $data_upload = json_decode($data_upload, true);
			if (empty($data_upload)) {
				$error_message = esc_html__('File upload is empty', 'opal-upsale-quantity-for-woocommerce');
			}
		}

		if (isset($error_message)) {
			$error = new \WP_Error( 'file_error', $error_message );
			if ( is_wp_error( $error ) ) {
				_default_wp_die_handler( $error->get_error_message(), 'OUQW' );
			}
		}

		update_option(OUQW_SETTINGS_KEY, $data_upload);
		// set_transient( 'ouqw_import_settings', 'yes',  10);
		// var_dump($error_message); die();
		$redirect = admin_url('admin.php?page=ouqw-settings');
		
		header("Location: $redirect");
		exit;
	}
}
