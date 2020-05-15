<?php

// If accessed directly, exit.
defined( 'ABSPATH' ) || exit;

if( !class_exists( 'WC_Product_CSV_Import_Suite_Ext_Import' ) ) :
    class WC_Product_CSV_Import_Suite_Ext_Import {
        /**
         * Setup class.
         * 
         * @since   1.0.0
         */
        function __construct() {
            add_action( 'woocommerce_csv_product_imported', __CLASS__ .'::productImported', 10, 3 );
            add_action( 'import_end', __CLASS__ .'::importEnd', 10, 0 );
        }

        /**
         * Get import ID by file path.
         * 
         * @param   string      $path       File path.
         * @return  int|bool
         * @since   1.0.0
         */
        public static function getImportIDByFilePath( $path ) {
            global $wpdb;

            return $wpdb->get_row(
                $wpdb->prepare( 
                    "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %s", 
                    "_wp_attached_file", 
                    $path 
                )
            );
        }

        /**
         * Product imported.
         * 
         * @param   object                      $product            The product.
         * @param   integer                     $productID          Current product ID on process.
         * @param   WC_PCSVIS_Product_Import    $wcProductImport    Product import instance.
         * @since   1.0.0
         */
        public static function productImported( $product, $productID, $wcProductImport ) {
            global $wpdb;

            $wpUploadDir = wp_upload_dir(); // Get wp upload dir
            $wpUploadBaseDir = str_replace( '\\', '/', $wpUploadDir[ 'basedir' ] ) .'/'; // Convert backwards slash from the base directory string
            $filepath = str_replace( $wpUploadBaseDir, '', $_POST[ 'file' ] ); // Remove base directory string from the file path

            $import = self::getImportIDByFilePath( $filepath ); // Get current imort id 

            if( $import ) :
                update_post_meta( $productID, '_wc_csv_import_suite_ext_last_import_id', $import->post_id ); // Add the current import id as product's meta data
            endif;
        }

        /**
         * On import end.
         * 
         * @since   1.0.0
         */
        public static function importEnd() {
            $wcProductImport = $GLOBALS[ 'WC_CSV_Product_Import' ];

            $lastImportedProduct = end( $wcProductImport->processed_posts ); // Get last imported/merged product
            $importID = get_post_meta( $lastImportedProduct, '_wc_csv_import_suite_ext_last_import_id', TRUE ); // Get import ID from the last imported product
            ?>
            <script type="text/javascript">
                (function( $ ) {
                    function deleteUnimportedProducts( import_id, limit = 0, offset = 0 ) {
                        return $.ajax({
                            url     : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                            method  : 'POST',
                            dataType    : 'json',
                            data        : {
                                action      : 'deleteUnimportedProducts',
                                import_id   : import_id,
                                limit       : limit,
                                offset      : offset
                            },
                            success     : function( data ) {
                                if( data.status ) {
                                    return deleteUnimportedProducts( data.import_id, 100, data.next ) // Delete unimported products
                                } else {
                                    $( '#import-progress tbody tr.complete td' ).text( 'Finished deleting unimported products.' )
                                    $( '#import-progress tfoot tr.importer-loading--delete' ).hide()
                                }
                            },
                            error       : function( error ) {
                                $( '#import-progress tbody tr.complete td' ).text( error.responseText )
                                $( '#import-progress tfoot tr.importer-loading--delete' ).hide()
                            }
                        })
                    }

                    $(function() {
                        <?php if( 'woocommerce_csv' == $wcProductImport->import_page && 0 < count( $wcProductImport->processed_posts ) ) : ?>  
                        $( '#import-progress tbody tr.complete td' ).text( 'Deleting unimported products...' )
                        $( '#import-progress tfoot' ).append( '<tr class="importer-loading--delete"><td colspan="5" style="height: 32px;background: url(<?php echo plugins_url( 'woocommerce-product-csv-import-suite' ); ?>/assets/images/ajax-loader.gif) no-repeat center center;"></td></tr>' )

                        deleteUnimportedProducts( '<?php echo $importID; ?>', 100, 0 ) // Delete unimported products
                        <?php endif; ?>
                    })
                })( jQuery );
            </script>
            <?php
        }

        /**
         * Get unimported products.
         * 
         * @param   int             $importID   Import ID.
         * @param   int|bool        $limit      Limit.
         * @param   int|bool        $offset     Offset.
         * @return  array|null
         * @since   1.0.0
         */
        public static function getUnimportedProducts( $importID, $limit = FALSE, $offset = FALSE ) {
            $args = [
                'post_type'         => 'product',
                'posts_per_page'    => $limit,
                'fields'            => 'ids',
                'meta_query'        => [
                    [
                        'key'           => '_wc_csv_import_suite_ext_last_import_id',
                        'value'         => $importID,
                        'compare'       => '!='
                    ]
                ]
            ];

            if(  $offset ) : // If offset is set
                $args[ 'offset' ] = $offset;
            endif;

            $products = get_posts( $args ); // Get products that will match the query

            if( !$products ) : // Get products without '_wc_csv_import_suite_ext_last_import_id' meta data
                unset( $args[ 'meta_query' ][ 0 ][ 'value' ] );
                $args[ 'meta_query' ][ 0 ][ 'compare' ] = 'NOT EXISTS';

                $products = get_posts( $args ); // Get products that will match the query
            endif;

            return $products;
        }
    }

    new WC_Product_CSV_Import_Suite_Ext_Import;
endif;
