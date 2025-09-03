<?php
// wp-content/themes/bootstrap-casino-theme/single.php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header(); 
?>

<div class="col-12 col-lg-8">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        $post_type = get_post_type();
        $post_id = get_the_ID();
    ?>
        <article <?php post_class('mb-4'); ?>>
            
            <!-- Header with title and meta -->
            <header class="mb-4 pb-3 border-bottom">
                <h1 class="h3 mb-3"><?php the_title(); ?></h1>
                
                <!-- Meta info based on post type -->
                <div class="text-muted small d-flex flex-wrap gap-3">
                    <?php if ( $post_type === 'post' ) : ?>
                        <span>
                            <i class="text-primary">📅</i> <?php the_time( get_option('date_format') ); ?>
                        </span>
                        <span>
                            <i class="text-primary">✍️</i> <?php the_author(); ?>
                        </span>
                        <?php if ( get_comments_number() > 0 ) : ?>
                            <span>
                                <i class="text-primary">💬</i> <?php comments_number(); ?>
                            </span>
                        <?php endif; ?>
                        
                    <?php elseif ( $post_type === 'casino' ) : ?>
                        <?php 
                        $rating = \BST\Helpers::get_casino_meta( $post_id, 'rating_overall' );
                        $year = \BST\Helpers::get_casino_meta( $post_id, 'year_est' );
                        $official = \BST\Helpers::get_casino_meta( $post_id, 'official_site' );
                        ?>
                        <?php if ( $rating ) : ?>
                            <span class="badge bg-dark">
                                ⭐ <?php echo number_format( $rating, 1 ); ?>/10
                            </span>
                        <?php endif; ?>
                        <?php if ( $year ) : ?>
                            <span>
                                <strong><?php _e('Established:', 'bst'); ?></strong> <?php echo esc_html( $year ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( $official ) : ?>
                            <span>
                                <a href="<?php echo esc_url( $official ); ?>" target="_blank" rel="nofollow noopener">
                                    🔗 <?php _e('Official Site', 'bst'); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        
                    <?php elseif ( $post_type === 'game' ) : ?>
                        <?php 
                        $casino_count = count( \BST\Helpers::casinos_linked_to_game( $post_id ) );
                        $categories = get_the_category();
                        ?>
                        <?php if ( $casino_count > 0 ) : ?>
                            <span>
                                <strong><?php _e('Available at:', 'bst'); ?></strong> 
                                <?php printf( _n( '%s casino', '%s casinos', $casino_count, 'bst' ), $casino_count ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( $categories ) : ?>
                            <span>
                                <strong><?php _e('Category:', 'bst'); ?></strong>
                                <?php foreach ( $categories as $cat ) : ?>
                                    <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>">
                                        <?php echo esc_html( $cat->name ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </header>
            
            <!-- Featured Image if exists -->
            <?php if ( has_post_thumbnail() && $post_type === 'post' ) : ?>
                <div class="mb-4">
                    <?php the_post_thumbnail('large', ['class' => 'img-fluid rounded shadow-sm']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Casino specific features -->
            <?php if ( $post_type === 'casino' ) : ?>
                <?php
                $loyalty = \BST\Helpers::get_casino_meta( $post_id, 'loyalty', 0 );
                $live = \BST\Helpers::get_casino_meta( $post_id, 'live_casino', 0 );
                $mobile = \BST\Helpers::get_casino_meta( $post_id, 'mobile_casino', 0 );
                $email = \BST\Helpers::get_casino_meta( $post_id, 'contact_email' );
                $games = \BST\Helpers::get_casino_games( $post_id );
                ?>
                
                <!-- Features badges -->
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge <?php echo $loyalty ? 'bg-success' : 'bg-secondary'; ?> fs-6">
                        <?php _e('Loyalty Program', 'bst'); ?>: <?php echo \BST\Helpers::esc_bool_label( $loyalty ); ?>
                    </span>
                    <span class="badge <?php echo $live ? 'bg-success' : 'bg-secondary'; ?> fs-6">
                        <?php _e('Live Casino', 'bst'); ?>: <?php echo \BST\Helpers::esc_bool_label( $live ); ?>
                    </span>
                    <span class="badge <?php echo $mobile ? 'bg-success' : 'bg-secondary'; ?> fs-6">
                        <?php _e('Mobile Casino', 'bst'); ?>: <?php echo \BST\Helpers::esc_bool_label( $mobile ); ?>
                    </span>
                    <?php if ( $email ) : ?>
                        <span class="badge bg-info fs-6">
                            ✉️ <?php echo esc_html( $email ); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Games list if available -->
                <?php if ( $games ) : ?>
                    <div class="alert alert-light border mb-4">
                        <h4 class="h6 mb-2"><?php _e('Available Games:', 'bst'); ?></h4>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ( $games as $game_id ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $game_id ) ); ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <?php echo esc_html( get_the_title( $game_id ) ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php elseif ( $post_type === 'game' ) : ?>
                <?php
                $casino_ids = \BST\Helpers::casinos_linked_to_game( $post_id );
                if ( $casino_ids ) :
                ?>
                    <!-- Where to play this game -->
                    <div class="alert alert-light border mb-4">
                        <h4 class="h6 mb-2"><?php _e('Play this game at:', 'bst'); ?></h4>
                        <ul class="list-unstyled mb-0">
                            <?php
                            $casinos = get_posts([
                                'post_type' => 'casino',
                                'post__in' => $casino_ids,
                                'orderby' => 'meta_value_num',
                                'meta_key' => 'rating_overall',
                                'order' => 'DESC'
                            ]);
                            foreach ( $casinos as $casino ) :
                                $rating = get_post_meta( $casino->ID, 'rating_overall', true );
                            ?>
                                <li class="mb-2">
                                    <a href="<?php echo esc_url( get_permalink( $casino->ID ) ); ?>">
                                        <?php echo esc_html( $casino->post_title ); ?>
                                    </a>
                                    <?php if ( $rating ) : ?>
                                        <span class="badge bg-secondary ms-2">
                                            <?php echo number_format( $rating, 1 ); ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Main content -->
            <div class="content mb-4">
                <?php the_content(); ?>
            </div>
            
            <!-- Tags for all post types -->
            <?php if ( has_tag() ) : ?>
                <div class="mb-4">
                    <strong class="d-inline-block mb-2"><?php _e('Tags:', 'bst'); ?></strong>
                    <?php the_tags('<span class="badge bg-light text-dark me-1">#', '</span> <span class="badge bg-light text-dark me-1">#', '</span>'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Navigation for posts -->
            <?php if ( $post_type === 'post' ) : ?>
                <nav class="d-flex justify-content-between border-top pt-3">
                    <div>
                        <?php previous_post_link('%link', '← %title'); ?>
                    </div>
                    <div class="text-end">
                        <?php next_post_link('%link', '%title →'); ?>
                    </div>
                </nav>
            <?php endif; ?>
            
        </article>
        
        <!-- Comments for regular posts -->
        <?php if ( $post_type === 'post' && comments_open() ) : ?>
            <div class="mt-4">
                <?php comments_template(); ?>
            </div>
        <?php endif; ?>
        
    <?php endwhile; endif; ?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>