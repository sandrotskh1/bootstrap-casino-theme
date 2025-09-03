<?php

class BST_Widget_Tabs extends WP_Widget {
    
    public function __construct() {
        parent::__construct( 
            'bst_widget_tabs', 
            __('BST Tabs Widget', 'bst'), 
            array( 'description' => __('Tabbed widget: categories or casinos', 'bst') )
        );
    }

    public function form( $instance ) {
        // Set defaults
        $defaults = array( 
            'title' => __('Highlights', 'bst'), 
            'mode' => 'casinos', 
            'cat1' => 0, 
            'cat2' => 0 
        );
        
        $instance = wp_parse_args( (array) $instance, $defaults );
        
        $title = esc_attr( $instance['title'] ); 
        $mode = esc_attr( $instance['mode'] );
        $cat1 = intval( $instance['cat1'] );   
        $cat2 = intval( $instance['cat2'] );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:', 'bst'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" 
                   type="text" 
                   value="<?php echo $title; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('mode'); ?>">
                <strong><?php _e('Mode', 'bst'); ?></strong>
            </label><br/>
            <select id="<?php echo $this->get_field_id('mode'); ?>" 
                    name="<?php echo $this->get_field_name('mode'); ?>" 
                    class="widefat">
                <option value="categories" <?php selected( $mode, 'categories' ); ?>>
                    <?php _e('Two Categories (Part 1)', 'bst'); ?>
                </option>
                <option value="casinos" <?php selected( $mode, 'casinos' ); ?>>
                    <?php _e('Casinos: Popular/Recent (Part 2)', 'bst'); ?>
                </option>
            </select>
        </p>
        
        <p>
            <label><?php _e('Category #1', 'bst'); ?></label>
            <?php wp_dropdown_categories( array(
                'show_option_all' => __('— Select —', 'bst'), 
                'name' => $this->get_field_name('cat1'), 
                'selected' => $cat1, 
                'class' => 'widefat', 
                'hide_empty' => false 
            )); ?>
        </p>
        
        <p>
            <label><?php _e('Category #2', 'bst'); ?></label>
            <?php wp_dropdown_categories( array(
                'show_option_all' => __('— Select —', 'bst'), 
                'name' => $this->get_field_name('cat2'), 
                'selected' => $cat2, 
                'class' => 'widefat', 
                'hide_empty' => false 
            )); ?>
        </p>
        
        <p class="description">
            <?php _e('Categories are used only in "Two Categories" mode.', 'bst'); ?>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['mode'] = ( in_array( $new_instance['mode'], array('categories','casinos') ) ) ? $new_instance['mode'] : 'casinos';
        $instance['cat1'] = intval( $new_instance['cat1'] );
        $instance['cat2'] = intval( $new_instance['cat2'] );
        
        return $instance;
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $mode = isset( $instance['mode'] ) ? $instance['mode'] : 'casinos';
        $cat1 = intval( $instance['cat1'] );
        $cat2 = intval( $instance['cat2'] );
        
        // Generate unique ID for tabs
        $widget_id = isset( $args['widget_id'] ) ? $args['widget_id'] : uniqid('bstw_');
        $uid = esc_attr( $widget_id );

        echo $args['before_widget'];
        
        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        // Build navigation tabs
        echo '<ul class="nav nav-tabs" role="tablist" id="tabs-'. $uid .'">';
        
        if ( $mode == 'categories' ) {
            echo '<li class="nav-item" role="presentation">';
            echo '<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1-'. $uid .'" type="button" role="tab" aria-controls="tab1-'. $uid .'" aria-selected="true">News</button>';
            echo '</li>';
            
            echo '<li class="nav-item" role="presentation">';
            echo '<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2-'. $uid .'" type="button" role="tab" aria-controls="tab2-'. $uid .'" aria-selected="false">Guides</button>';
            echo '</li>';
        } else {
            echo '<li class="nav-item" role="presentation">';
            echo '<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1-'. $uid .'" type="button" role="tab" aria-controls="tab1-'. $uid .'" aria-selected="true">'.esc_html__('Popular', 'bst').'</button>';
            echo '</li>';
            
            echo '<li class="nav-item" role="presentation">';
            echo '<button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2-'. $uid .'" type="button" role="tab" aria-controls="tab2-'. $uid .'" aria-selected="false">'.esc_html__('Recent', 'bst').'</button>';
            echo '</li>';
        }
        
        echo '</ul>';

        echo '<div class="tab-content pt-3">';
        
        echo '<div id="tab1-'. $uid .'" class="tab-pane fade show active" role="tabpanel">';
        if ( $mode == 'categories' ) { 
            $this->display_category_posts( $cat1 ); 
        } else { 
            $this->display_popular_casinos(); 
        }
        echo '</div>';

        echo '<div id="tab2-'. $uid .'" class="tab-pane fade" role="tabpanel">';
        if ( $mode == 'categories' ) { 
            $this->display_category_posts( $cat2 ); 
        } else { 
            $this->display_recent_casinos(); 
        }
        echo '</div>';
        
        echo '</div>';

        echo $args['after_widget'];
    }

    private function display_category_posts( $cat_id ) {
        if ( ! $cat_id ) { 
            echo '<em>'.esc_html__('Select a category in widget settings.', 'bst').'</em>'; 
            return; 
        }
        
        $query_args = array(
            'posts_per_page' => 5,
            'cat' => $cat_id,
            'no_found_rows' => true
        );
        
        $posts = new WP_Query( $query_args );
        
        if ( $posts->have_posts() ) {
            echo '<ul class="list-unstyled mb-0">';
            
            while ( $posts->have_posts() ) { 
                $posts->the_post();
                
                echo '<li class="mb-2">';
                echo '<a href="' . esc_url( get_permalink() ) . '">';
                echo esc_html( get_the_title() );
                echo '</a>';
                echo '</li>';
            }
            
            echo '</ul>';
            wp_reset_postdata();
        } else { 
            echo '<em>'.esc_html__('No posts.', 'bst').'</em>'; 
        }
    }

    private function display_popular_casinos() {
        $query_args = array(
            'post_type' => 'casino',
            'posts_per_page' => 3,
            'meta_key' => 'rating_overall',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'no_found_rows' => true,
        );
        
        $casinos = new WP_Query( $query_args );
        $this->render_casino_list( $casinos );
    }

    private function display_recent_casinos() {
        $query_args = array(
            'post_type' => 'casino',
            'posts_per_page' => 3,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        );
        
        $casinos = new WP_Query( $query_args );
        $this->render_casino_list( $casinos );
    }

    private function render_casino_list( WP_Query $casino_query ) {
        if ( $casino_query->have_posts() ) {
            echo '<ul class="list-unstyled mb-0">';
            
            while ( $casino_query->have_posts() ) { 
                $casino_query->the_post();
                
                $rating = get_post_meta( get_the_ID(), 'rating_overall', true );
                $rating_display = $rating ? number_format_i18n( $rating, 1 ) : '-';
                
                $thumbnail = get_the_post_thumbnail( get_the_ID(), 'thumbnail', array(
                    'class' => 'rounded', 
                    'style' => 'width:40px;height:40px;object-fit:cover;', 
                    'alt' => esc_attr( get_the_title() )
                ));
                
                echo '<li class="mb-2 d-flex align-items-center gap-2">';
                echo $thumbnail . ' ';
                echo '<a href="' . esc_url( get_permalink() ) . '">';
                echo esc_html( get_the_title() );
                echo '</a> ';
                echo '<span class="badge bg-secondary ms-auto">' . esc_html( $rating_display ) . '</span>';
                echo '</li>';
            }
            
            echo '</ul>';
            wp_reset_postdata();
        } else { 
            echo '<em>'.esc_html__('No casinos.', 'bst').'</em>'; 
        }
    }
}
