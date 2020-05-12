<?php

/**
 * Plugin Name:       WooCommerce Product CSV Import Suite - Extension
 * Plugin URI:        https://professionalwebsolutions.com.au/
 * Description:       WooCommerce Product CSV Import Suite plugin extension.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Professional Web Solutions
 * Author URI:        https://professionalwebsolutions.com.au/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woocommerce-product-csv-import-suite-extension
 * Domain Path:       /languages
 */

!defined( 'WC_IMPORT_SUITE_EXT_PLUGIN_VERSION' ) ? define( 'WC_IMPORT_SUITE_EXT_PLUGIN_VERSION', '1.0.0' ) : '';
!defined( 'WC_IMPORT_SUITE_EXT_PLUGIN_DIR_PATH' ) ? define( 'WC_IMPORT_SUITE_EXT_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) ) : '';
!defined( 'WC_IMPORT_SUITE_EXT_PLUGIN_DIR_URI' ) ? define( 'WC_IMPORT_SUITE_EXT_PLUGIN_DIR_URI', plugin_dir_url( __FILE__ ) ) : '';
!defined( 'WC_IMPORT_SUITE_EXT_TEXT_DOMAIN' ) ? define( 'WC_IMPORT_SUITE_EXT_TEXT_DOMAIN', 'woocommerce-product-csv-import-suite-extension' ) : '';

require_once WC_IMPORT_SUITE_EXT_PLUGIN_DIR_PATH .'includes/woocommerce-product-csv-import-suite-extension.class.php';