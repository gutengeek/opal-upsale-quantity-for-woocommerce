<?php
/**
 * Opal Upsale Quantity for Woocommerce
 *
 * @package       opal-upsale-quantity-for-woocommerce
 * @author        WPOPAL
 * @version       1.1.2
 *
 * @wordpress-plugin
 * Plugin Name:   Opal Upsale Quantity for Woocommerce
 * Plugin URI:    https://wpopal.com/contact/
 * Description:   Our plugin ensures that your customers receive accurate delivery estimates every time.
 * Version:       1.1.2
 * Author:        WPOPAL
 * Author URI:    https://wpopal.com
 * License:       GPLv2 or later
 * License URI:   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:   opal-upsale-quantity-for-woocommerce
 * Domain Path:   /languages
 * Requires Plugins: woocommerce
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name
define( 'OUQW_NAME', 'Opal Upsale Quantity for Woocommerce' );
define( 'OUQW_TEXTDOMAIN', 'opal-upsale-quantity-for-woocommerce' );

// Plugin version
define( 'OUQW_VERSION', '1.1.2' );

// Plugin Root File
define( 'OUQW_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'OUQW_PLUGIN_BASE', plugin_basename( OUQW_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'OUQW_PLUGIN_DIR',	plugin_dir_path( OUQW_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'OUQW_PLUGIN_URL',	plugin_dir_url( OUQW_PLUGIN_FILE ) );

define(	'OUQW_UPLOAD_DIR', 'ouqw_uploads' );
define(	'OUQW_CRON_HOOK', 'ouqw_daily_event' );
define(	'OUQW_SETTINGS_KEY', 'ouqw_settings_key' );

/**
 * Load the main class for the core functionality
 */
require_once OUQW_PLUGIN_DIR . 'includes/class-opal-upsale-quantity-for-woocommerce.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  WPOPAL
 * @since   1.1.2
 * @return  object|OUQW_Start_Instance
 */
function ouqw() {
	return OUQW_Start_Instance::instance();
}
ouqw();
