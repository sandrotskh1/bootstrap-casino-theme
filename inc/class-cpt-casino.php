<?php
/**
 * Casino Custom Post Type
 *
 * @package BST
 */

namespace BST;

if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_Casino {
    
    /**
     * Register post type
     */
    public static function register() {
        $labels = [
            'name'               => __( 'Casinos', 'bst' ),
            'singular_name'      => __( 'Casino', 'bst' ),
            'add_new'            => __( 'Add New', 'bst' ),
            'add_new_item'       => __( 'Add New Casino', 'bst' ),
            'edit_item'          => __( 'Edit Casino', 'bst' ),
            'new_item'           => __( 'New Casino', 'bst' ),
            'view_item'          => __( 'View Casino', 'bst' ),
            'search_items'       => __( 'Search Casinos', 'bst' ),
            'not_found'          => __( 'No casinos found', 'bst' ),
            'not_found_in_trash' => __( 'No casinos found in Trash', 'bst' ),
        ];

        register_post_type( 'casino', [
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'casino' ],
            'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'taxonomies'   => [ 'category', 'post_tag' ],
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-currency-dollar',
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
            'bst_casino_details', 
            __( 'Casino Details', 'bst' ), 
            [ __CLASS__, 'render_details' ], 
            'casino', 
            'normal', 
            'default' 
        );
        
        add_meta_box( 
            'bst_casino_ratings', 
            __( 'Casino Ratings', 'bst' ), 
            [ __CLASS__, 'render_ratings' ], 
            'casino', 
            'normal', 
            'default' 
        );
        
        add_meta_box( 
            'bst_casino_features', 
            __( 'Casino Features', 'bst' ), 
            [ __CLASS__, 'render_features' ], 
            'casino', 
            'side', 
            'default' 
        );
        
        add_meta_box( 
            'bst_casino_games', 
            __( 'Linked Games', 'bst' ), 
            [ __CLASS__, 'render_games' ], 
            'casino', 
            'side', 
            'default' 
        );
    }
    
    /**
     * Render details meta box
     */
    public static function render_details( $post ) {
        wp_nonce_field( 'bst_casino_meta', 'bst_casino_meta_nonce' );
        
        $site_url = esc_url( Helpers::get_casino_meta( $post->ID, 'official_site' ) );
        $year = esc_attr( Helpers::get_casino_meta( $post->ID, 'year_est' ) );
        $contact = esc_attr( Helpers::get_casino_meta( $post->ID, 'contact_email' ) );
        ?>
        <div class="grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <p>
                <label><strong><?php _e('Official Site', 'bst'); ?></strong></label><br/>
                <input type="url" name="official_site" value="<?php echo $site_url; ?>" class="widefat" placeholder="https://...">
            </p>
            <p>
                <label><strong><?php _e('Year Established', 'bst'); ?></strong></label><br/>
                <input type="text" name="year_est" value="<?php echo $year; ?>" class="widefat" placeholder="1998">
            </p>
            <p>
                <label><strong><?php _e('Contact Email', 'bst'); ?></strong></label><br/>
                <input type="email" name="contact_email" value="<?php echo $contact; ?>" class="widefat" placeholder="info@example.com">
            </p>
        </div>
        <?php
    }
    
    /**
     * Render ratings meta box
     */
    public static function render_ratings( $post ) {
        $rating_fields = Helpers::rating_fields();
        echo '<div class="grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">';
        
        foreach ( $rating_fields as $field_key => $field_label ) {
            $value = esc_attr( Helpers::get_casino_meta( $post->ID, $field_key ) );
            printf(
                '<p><label><strong>%1$s</strong></label><br/>' .
                '<input type="number" step="0.1" min="1" max="10" name="%2$s" value="%3$s" class="small-text" style="width:120px;"></p>',
                esc_html( $field_label ), 
                esc_attr( $field_key ), 
                $value
            );
        }
        
        $total_rating = Helpers::get_casino_meta( $post->ID, 'rating_overall' );
        echo '<p><strong>' . esc_html__( 'Overall Rating: ', 'bst' ) . '</strong> ' . esc_html( $total_rating ) . '</p>';
        echo '</div>';
    }
    
    /**
     * Render features meta box
     */
    public static function render_features( $post ) {
        $has_loyalty = Helpers::get_casino_meta( $post->ID, 'loyalty', 0 );
        $has_live = Helpers::get_casino_meta( $post->ID, 'live_casino', 0 );
        $has_mobile = Helpers::get_casino_meta( $post->ID, 'mobile_casino', 0 );
        ?>
        <p>
            <label>
                <input type="checkbox" name="loyalty" value="1" <?php checked( $has_loyalty, 1 ); ?>> 
                <?php _e('Loyalty Program', 'bst'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="live_casino" value="1" <?php checked( $has_live, 1 ); ?>> 
                <?php _e('Live Casino', 'bst'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="mobile_casino" value="1" <?php checked( $has_mobile, 1 ); ?>> 
                <?php _e('Mobile Casino', 'bst'); ?>
            </label>
        </p>
        <?php
    }
    
    /**
     * Render games meta box
     */
    public static function render_games( $post ) {
        $all_games = get_posts([
            'post_type' => 'game',
            'numberposts' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        ]);
        
        $linked_games = Helpers::get_casino_games( $post->ID );
        
        echo '<div style="max-height:220px;overflow:auto;">';
        
        if ( $all_games ) {
            foreach ( $all_games as $game ) {
                $is_checked = in_array( $game->ID, $linked_games, true ) ? 'checked' : '';
                printf(
                    '<label style="display:block;margin:4px 0;">' .
                    '<input type="checkbox" name="games[]" value="%1$d" %3$s /> %2$s' .
                    '</label>',
                    $game->ID, 
                    esc_html( get_the_title( $game ) ), 
                    $is_checked
                );
            }
        } else {
            echo '<em>' . esc_html__( 'No games available.', 'bst' ) . '</em>';
        }
        
        echo '</div>';
    }
    
    /**
     * Save post data
     */
    public static function save( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( wp_is_post_revision( $post_id ) ) return;
        if ( get_post_type( $post_id ) !== 'casino' ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        
        if ( isset( $_POST['bst_casino_meta_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_POST['bst_casino_meta_nonce'], 'bst_casino_meta' ) ) {
                return;
            }
        }
        
        $site_url = isset( $_POST['official_site'] ) ? esc_url_raw( $_POST['official_site'] ) : '';
        $year = isset( $_POST['year_est'] ) ? sanitize_text_field( $_POST['year_est'] ) : '';
        $contact = isset( $_POST['contact_email'] ) ? sanitize_email( $_POST['contact_email'] ) : '';
        
        update_post_meta( $post_id, 'official_site', $site_url );
        update_post_meta( $post_id, 'year_est', $year );
        update_post_meta( $post_id, 'contact_email', $contact );
        
        foreach ( Helpers::rating_fields() as $field_key => $field_label ) {
            if ( isset( $_POST[ $field_key ] ) && $_POST[ $field_key ] !== '' ) {
                $rating_value = floatval( $_POST[ $field_key ] );
                $rating_value = max( 1.0, min( 10.0, $rating_value ) );
                update_post_meta( $post_id, $field_key, $rating_value );
            } else {
                delete_post_meta( $post_id, $field_key );
            }
        }
        
        Helpers::compute_overall_rating( $post_id );
        
        update_post_meta( $post_id, 'loyalty', Helpers::sanitize_bool( isset( $_POST['loyalty'] ) ) );
        update_post_meta( $post_id, 'live_casino', Helpers::sanitize_bool( isset( $_POST['live_casino'] ) ) );
        update_post_meta( $post_id, 'mobile_casino', Helpers::sanitize_bool( isset( $_POST['mobile_casino'] ) ) );
        
        $game_ids = isset( $_POST['games'] ) ? array_map( 'intval', (array) $_POST['games'] ) : [];
        Helpers::set_casino_games( $post_id, $game_ids );
    }
}
