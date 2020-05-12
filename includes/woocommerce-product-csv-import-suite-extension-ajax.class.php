<?php

// If accessed directly, exit.
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'WC_Product_CSV_Import_Suite_Ext_Ajax' ) ) :
    class WC_Product_CSV_Import_Suite_Ext_Ajax {
        /**
         * Setup class.
         * 
         * @since   1.0.0
         */
        function __construct() {
            add_action( 'wp_ajax_nopriv_deleteUnimportedProducts', __CLASS__ .'::deleteUnimportedProducts' );
            add_action( 'wp_ajax_deleteUnimportedProducts', __CLASS__ .'::deleteUnimportedProducts' );
        }

        /**
         * Delete unimported/unmerged products.
         * 
         * @since   1.0.0
         */
        public static function deleteUnimportedProducts() {
            if( !defined( 'DOING_AJAX' ) && !DOING_AJAX ) 
                die();

            $results = [
                'status'    => 1,
                'next'      => 0,
                'items'     => []
            ];

            $products = WC_Product_CSV_Import_Suite_Ext_Import::getUnimportedProducts( $_POST[ 'limit' ], $_POST[ 'offset' ] );
            
            if( 0 < count( $products ) ) :
                $results[ 'next' ] = intval( $_POST[ 'limit' ] ) + intval( $_POST[ 'offset' ] );
                $results[ 'items' ] = $products;

                foreach( $products as $product ) :
                    global $wpdb;

                    $wpdb->delete( "{$wpdb->prefix}posts", [ 'ID' => $product->ID ], [ '%d' ] ); // Delete products
                    $wpdb->delete( "{$wpdb->prefix}postmeta", [ 'post_id' => $product->ID ], [ '%d' ] ); // Delete product meta datas
                    $wpdb->delete( "{$wpdb->prefix}term_relationships", [ 'object_id' => $product->ID ], [ '%d' ] ); // Delete product taxonomies
                endforeach;
            else :
                $results[ 'status' ] = 0;
            endif;

            wp_send_json( $results );   
        }
    }

    new WC_Product_CSV_Import_Suite_Ext_Ajax;
endif;