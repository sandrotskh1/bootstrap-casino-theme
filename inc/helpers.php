<?php
namespace BST;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Helpers {
    
    public static function sanitize_bool( $value ) { 
        return $value ? 1 : 0; 
    }
    
    public static function esc_bool_label( $value ) { 
        return $value ? __('YES', 'bst') : __('NO', 'bst'); 
    }

    public static function rating_fields() {
        return array(
            'rating_games'             => __('Games', 'bst'),
            'rating_live_casino'       => __('Live Casino', 'bst'),
            'rating_payout'            => __('Payout', 'bst'),
            'rating_licensing'         => __('Licensing', 'bst'),
            'rating_payment_methods'   => __('Payment Methods', 'bst'),
            'rating_withdrawal_speed'  => __('Withdrawal Speed', 'bst'),
            'rating_support'           => __('Support', 'bst'),
            'rating_offers'            => __('Offers', 'bst'),
            'rating_mobile'            => __('Mobile', 'bst'),
            'rating_website'           => __('Website', 'bst'),
        );
    }

    public static function compute_overall_rating( $post_id ) {
        $rating_fields = self::rating_fields();
        $total = 0; 
        $counter = 0;
        
        foreach ( $rating_fields as $field_key => $field_label ) {
            $rating_value = get_post_meta( $post_id, $field_key, true );
            
            if ( $rating_value !== '' ) { 
                $total += floatval( $rating_value ); 
                $counter++; 
            }
        }
        
        if ( $counter === 0 ) {
            return '';
        }
        
        $average = round( $total / $counter, 1 );
        update_post_meta( $post_id, 'rating_overall', $average );
        
        return $average;
    }

    public static function get_casino_meta( $post_id, $meta_key, $fallback = '' ) {
        $meta_value = get_post_meta( $post_id, $meta_key, true );
        return $meta_value === '' ? $fallback : $meta_value;
    }

    public static function get_casino_games( $casino_id ) {
        $game_ids = get_post_meta( $casino_id, 'games', true );
        
        if ( ! is_array( $game_ids ) ) {
            $game_ids = array();
        }
        
        return array_map( 'intval', $game_ids );
    }

    public static function set_casino_games( $casino_id, $game_ids ) {
        $clean_ids = array_values( 
            array_unique( 
                array_map( 'intval', (array) $game_ids ) 
            ) 
        );
        
        update_post_meta( $casino_id, 'games', $clean_ids );
    }

    public static function casinos_linked_to_game( $game_id ) {
        $query_args = array(
            'post_type'      => 'casino',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array( 
                    'key' => 'games', 
                    'value' => '"' . intval($game_id) . '"', 
                    'compare' => 'LIKE' 
                )
            ),
            'fields'         => 'ids',
            'no_found_rows'  => true,
        );
        
        $casino_query = new \WP_Query( $query_args );
        return $casino_query->posts;
    }
}
