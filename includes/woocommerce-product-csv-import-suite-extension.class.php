<?php

// If accessed directly, exit.
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'WC_Product_CSV_Import_Suite_Ext' ) ) :
    class WC_Product_CSV_Import_Suite_Ext {
        /**
         * Setup class.
         * 
         * @since   1.0.0
         */
        function __construct() {
            register_activation_hook( __FILE__, __CLASS__ .'::activate' );
            register_deactivation_hook( __FILE__, __CLASS__ .'::deactivate' );

            add_action( 'plugins_loaded', __CLASS__ .'::includes' );
        }

        /**
         * Activate.
         * 
         * @since   1.0.0
         */
        public static function activate() {

        }

        /**
         * Deactivate.
         * 
         * @since   1.0.0
         */
        public static function deactivate() {
            
        }

        /**
         * Includes.
         * 
         * @since   1.0.0
         */
        public static function includes() {
            require_once WC_IMPORT_SUITE_EXT_PLUGIN_DIR_PATH .'includes/woocommerce-product-csv-import-suite-extension-ajax.class.php';
            require_once WC_IMPORT_SUITE_EXT_PLUGIN_DIR_PATH .'includes/woocommerce-product-csv-import-suite-extension-import.class.php';
        }
    }

    new WC_Product_CSV_Import_Suite_Ext;
endif;