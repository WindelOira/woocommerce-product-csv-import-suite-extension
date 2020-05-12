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
            add_action( 'import_end', __CLASS__ .'::importEnd', 10, 0 );
        }

        /**
         * On import end.
         * 
         * @since   1.0.0
         */
        public static function importEnd() {
            $wcImporter = $GLOBALS[ 'WC_CSV_Product_Import' ];
            ?>
            <script type="text/javascript">
                (function( $ ) {
                    function deleteUnimportedProducts( limit = 0, offset = 0 ) {
                        return $.ajax({
                            url     : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                            method  : 'POST',
                            dataType    : 'json',
                            data        : {
                                action      : 'deleteUnimportedProducts',
                                limit       : limit,
                                offset      : offset
                            },
                            success     : function( data ) {
                                if( data.status ) {
                                    return deleteUnimportedProducts( 100, data.next ) // Delete unimported products
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
                        <?php if( 'woocommerce_csv' == $wcImporter->import_page && 0 < count( $wcImporter->processed_posts ) ) : ?>  
                        $( '#import-progress tbody tr.complete td' ).text( 'Deleting unimported products...' )
                        $( '#import-progress tfoot' ).append( '<tr class="importer-loading--delete"><td colspan="5" style="height: 32px;background: url(<?php echo plugins_url( 'woocommerce-product-csv-import-suite' ); ?>/assets/images/ajax-loader.gif) no-repeat center center;"></td></tr>' )

                        deleteUnimportedProducts( 100, 0 ) // Delete unimported products
                        <?php endif; ?>
                    })
                })( jQuery );
            </script>
            <?php
        }

        /**
         * Get unimported products.
         * 
         * @param   int|bool        $limit      Limit.
         * @param   int|bool        $offset     Offset.
         * @return  array|null
         * @since   1.0.0
         */
        public static function getUnimportedProducts( $limit = FALSE, $offset = FALSE ) {
            global $wpdb;

            if( $limit && $offset ) :
                $products = $wpdb->get_results(
                    $wpdb->prepare( 
                        "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s AND DATE( post_modified ) < DATE( NOW() ) LIMIT %d, %d", 
                        "product", 
                        intval( $offset ), 
                        intval( $limit ) 
                    )
                );
            else :
                $products = $wpdb->get_results(
                    $wpdb->prepare( 
                        "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s AND DATE( post_modified ) < DATE( NOW() )", 
                        "product" 
                    )
                );
            endif;

            return $products;
        }
    }

    new WC_Product_CSV_Import_Suite_Ext_Import;
endif;
