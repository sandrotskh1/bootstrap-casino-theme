<?php
/**
 * AJAX Handler Class
 *
 * @package BST
 */

namespace BST;

if ( ! defined( 'ABSPATH' ) ) exit;

class BST_Ajax {
    
    /**
     * Register AJAX handlers
     */
    public static function register() {
        add_action( 'wp_ajax_bst_template2_change', [ __CLASS__, 'template2_change' ] );
        add_action( 'wp_ajax_nopriv_bst_template2_change', [ __CLASS__, 'template2_change' ] );
    }
    
    /**
     * Handle template 2 field change
     */
    public static function template2_change() {
        check_ajax_referer( 'bst_nonce', 'nonce' );
        
        $selected_field = isset( $_POST['field'] ) ? sanitize_key( $_POST['field'] ) : 'loyalty';
        
        $args = [
            'post_type'      => 'casino',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'rating_overall',
            'order'          => 'DESC',
            'no_found_rows'  => true,
            'fields'         => 'ids',
        ];
        
        $query = new \WP_Query( $args );
        
        ob_start();
        
        if ( $query->posts ) {
            foreach ( $query->posts as $casino_id ) {
                echo Shortcode_Casinos::render_row( $casino_id, '2', $selected_field );
            }
        } else {
            echo '<tr><td colspan="3"><em>' . esc_html__( 'No casinos available.', 'bst' ) . '</em></td></tr>';
        }
        
        $output = ob_get_clean();
        
        wp_send_json_success( [ 'rows' => $output ] );
    }
}
