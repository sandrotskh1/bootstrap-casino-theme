<?php
/**
 * Game Custom Post Type
 *
 * @package BST
 */

namespace BST;

if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_Game {
    
    /**
     * Register post type
     */
    public static function register() {
        $labels = [
            'name'               => __( 'Games', 'bst' ),
            'singular_name'      => __( 'Game', 'bst' ),
            'add_new'            => __( 'Add New', 'bst' ),
            'add_new_item'       => __( 'Add New Game', 'bst' ),
            'edit_item'          => __( 'Edit Game', 'bst' ),
            'new_item'           => __( 'New Game', 'bst' ),
            'view_item'          => __( 'View Game', 'bst' ),
            'search_items'       => __( 'Search Games', 'bst' ),
            'not_found'          => __( 'No games found', 'bst' ),
            'not_found_in_trash' => __( 'No games found in Trash', 'bst' ),
        ];

        register_post_type( 'game', [
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'games' ],
            'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'taxonomies'   => [ 'category', 'post_tag' ],
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-controls-play',
        ]);
    }
    
    /**
     * Register admin meta boxes
     */
    public static function admin() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ] );
    }
    
    /**
     * Add meta boxes
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'bst_game_casinos',
            __( 'Linked Casinos', 'bst' ),
            [ __CLASS__, 'render_casinos_box' ],
            'game',
            'side',
            'default'
        );
    }
    
    /**
     * Render casinos meta box
     */
    public static function render_casinos_box( $post ) {
        wp_nonce_field( 'bst_game_casinos', 'bst_game_casinos_nonce' );
        
        $all_casinos = get_posts([
            'post_type'   => 'casino',
            'numberposts' => -1,
            'post_status' => 'publish',
            'orderby'     => 'title',
            'order'       => 'ASC',
        ]);
        
        $linked_ids = Helpers::casinos_linked_to_game( $post->ID );
        
        echo '<div class="bst-checkbox-list" style="max-height:220px;overflow:auto;">';
        
        if ( $all_casinos ) {
            foreach ( $all_casinos as $item ) {
                $is_checked = in_array( $item->ID, $linked_ids, true ) ? 'checked' : '';
                printf(
                    '<label style="display:block;margin:4px 0;">' .
                    '<input type="checkbox" name="bst_linked_casinos[]" value="%1$d" %3$s /> %2$s' .
                    '</label>',
                    $item->ID,
                    esc_html( get_the_title( $item ) ),
                    $is_checked
                );
            }
        } else {
            echo '<em>' . esc_html__( 'No casinos available.', 'bst' ) . '</em>';
        }
        
        echo '</div>';
        echo '<p class="description">' . esc_html__( 'Select casinos where this game is available.', 'bst' ) . '</p>';
    }
    
    /**
     * Save post data
     */
    public static function save( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( wp_is_post_revision( $post_id ) ) return;
        if ( get_post_type( $post_id ) !== 'game' ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        
        if ( isset( $_POST['bst_game_casinos_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_POST['bst_game_casinos_nonce'], 'bst_game_casinos' ) ) {
                return;
            }
        }
        
        $new_casinos = isset( $_POST['bst_linked_casinos'] ) ? array_map( 'intval', (array) $_POST['bst_linked_casinos'] ) : [];
        $new_casinos = array_values( array_unique( $new_casinos ) );
        
        $existing_casinos = Helpers::casinos_linked_to_game( $post_id );
        
        $casinos_to_remove = array_diff( $existing_casinos, $new_casinos );
        foreach ( $casinos_to_remove as $casino_id ) {
            $casino_games = Helpers::get_casino_games( $casino_id );
            $casino_games = array_diff( $casino_games, [ $post_id ] );
            Helpers::set_casino_games( $casino_id, $casino_games );
        }
        
        $casinos_to_add = array_diff( $new_casinos, $existing_casinos );
        foreach ( $casinos_to_add as $casino_id ) {
            $casino_games = Helpers::get_casino_games( $casino_id );
            if ( ! in_array( $post_id, $casino_games, true ) ) {
                $casino_games[] = $post_id;
                Helpers::set_casino_games( $casino_id, $casino_games );
            }
        }
    }
}
