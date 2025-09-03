<?php
/**
 * Casinos Shortcode Class
 *
 * @package BST
 */

namespace BST;

if ( ! defined( 'ABSPATH' ) ) exit;

class Shortcode_Casinos {
    
    /**
     * Register shortcode
     */
    public static function register() {
        add_shortcode( 'casinos', [ __CLASS__, 'render' ] );
    }
    
    /**
     * Render shortcode output
     */
    public static function render( $atts ) {
        $defaults = [
            'title' => __('Best Casino', 'bst'),
            'template' => '1',
            'second_col' => 'loyalty'
        ];
        
        $atts = shortcode_atts( $defaults, $atts, 'casinos' );
        
        $template = in_array( $atts['template'], ['1','2'], true ) ? $atts['template'] : '1';
        $column = in_array( $atts['second_col'], ['loyalty','live_casino','mobile_casino'], true ) ? $atts['second_col'] : 'loyalty';
        
        $args = [
            'post_type'      => 'casino',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'rating_overall',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ];
        
        $query = new \WP_Query( $args );
        
        ob_start();
        
        $container_id = 'bst-casinos-' . wp_generate_uuid4();
        
        echo '<div class="bst-casinos-table mb-4" id="' . esc_attr($container_id) . '" data-template="' . esc_attr($template) . '">';
        
        if ( $template === '2' ) {
            self::render_dropdown( $column );
        }
        
        echo '<h3 class="h5 mb-3">' . esc_html( $atts['title'] ) . '</h3>';
        echo '<div class="table-responsive">';
        echo '<table class="table align-middle">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Casino', 'bst') . '</th>';
        
        $col_class = ( $template === '2' ) ? ' class="d-none d-sm-table-cell"' : '';
        echo '<th' . $col_class . '>' . esc_html__('Details', 'bst') . '</th>';
        echo '<th>' . esc_html__('Action', 'bst') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                echo self::render_row( get_the_ID(), $template, $column );
            }
            wp_reset_postdata();
        } else {
            echo '<tr><td colspan="3"><em>' . esc_html__('No casinos available.', 'bst') . '</em></td></tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        
        return ob_get_clean();
    }
    
    /**
     * Render dropdown selector
     */
    private static function render_dropdown( $selected ) {
        $options = [
            'loyalty' => __('Loyalty', 'bst'),
            'live_casino' => __('Live Casino', 'bst'),
            'mobile_casino' => __('Mobile Casino', 'bst'),
            'year_est' => __('Year Established', 'bst'),
            'contact_email' => __('Contact Email', 'bst'),
            'games' => __('Games', 'bst')
        ];
        
        echo '<div class="mb-2 d-none d-sm-block">';
        echo '<label class="form-label me-2"><strong>' . esc_html__('Show in middle column:', 'bst') . '</strong></label>';
        echo '<select class="form-select d-inline-block w-auto bst-middle-select">';
        
        foreach ( $options as $key => $label ) {
            $is_selected = ( $key === $selected ) ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $is_selected . '>' . esc_html($label) . '</option>';
        }
        
        echo '</select>';
        echo '</div>';
    }
    
    /**
     * Render table row
     */
    public static function render_row( $id, $template, $field ) {
        $site_url = Helpers::get_casino_meta( $id, 'official_site' );
        $score = Helpers::get_casino_meta( $id, 'rating_overall' );
        $name = get_the_title( $id );
        
        $thumbnail = get_the_post_thumbnail( $id, 'thumbnail', [
            'class' => 'rounded me-2',
            'style' => 'width:56px;height:56px;object-fit:cover;',
            'alt' => esc_attr( $name )
        ]);
        
        $image = $thumbnail;
        if ( $site_url ) {
            $image = '<a href="' . esc_url($site_url) . '" target="_blank" rel="nofollow noopener">' . $image . '</a>';
        }
        
        $first_col = '<div class="d-flex align-items-center gap-2">' . $image;
        $first_col .= '<div><div class="fw-semibold">' . esc_html($name) . '</div>';
        $first_col .= '<small class="text-muted">' . esc_html__('Rating:', 'bst') . ' ';
        $first_col .= esc_html( $score !== '' ? number_format_i18n( floatval($score), 1 ) : '-' );
        $first_col .= '</small></div></div>';
        
        $second_col = self::get_field_value( $id, $field );
        
        $btn_url = $site_url ? $site_url : get_permalink( $id );
        $btn_attrs = $site_url ? ' target="_blank" rel="nofollow noopener"' : '';
        $btn_text = esc_html__('Review', 'bst');
        
        $third_col = '<a class="btn btn-primary btn-sm" href="' . esc_url($btn_url) . '"' . $btn_attrs . '>' . $btn_text . '</a>';
        
        $col_class = ( $template === '2' ) ? ' class="d-none d-sm-table-cell bst-middle-cell"' : ' class="bst-middle-cell"';
        
        return '<tr data-casino-id="' . esc_attr($id) . '">' .
               '<td>' . $first_col . '</td>' .
               '<td' . $col_class . '>' . $second_col . '</td>' .
               '<td>' . $third_col . '</td>' .
               '</tr>';
    }
    
    /**
     * Get field display value
     */
    public static function get_field_value( $id, $field ) {
        switch ( $field ) {
            case 'loyalty':
            case 'live_casino':
            case 'mobile_casino':
                $val = Helpers::get_casino_meta( $id, $field, 0 );
                $class = $val ? 'bg-success' : 'bg-secondary';
                return '<span class="badge ' . $class . '">' . esc_html( Helpers::esc_bool_label( $val ) ) . '</span>';
                
            case 'year_est':
                $val = Helpers::get_casino_meta( $id, 'year_est' );
                return $val ? esc_html( $val ) : '—';
                
            case 'contact_email':
                $val = Helpers::get_casino_meta( $id, 'contact_email' );
                return $val ? '<a href="mailto:' . esc_attr($val) . '">' . esc_html($val) . '</a>' : '—';
                
            case 'games':
                $game_ids = Helpers::get_casino_games( $id );
                if ( ! $game_ids ) return '—';
                
                $output = '<ul class="mb-0">';
                foreach ( $game_ids as $game_id ) {
                    $output .= '<li><a href="' . esc_url( get_permalink( $game_id ) ) . '">';
                    $output .= esc_html( get_the_title( $game_id ) ) . '</a></li>';
                }
                $output .= '</ul>';
                return $output;
                
            default:
                return '—';
        }
    }
}
