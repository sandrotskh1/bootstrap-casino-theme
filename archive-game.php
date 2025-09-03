<?php
// wp-content/themes/bootstrap-casino-theme/archive-game.php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header(); ?>

  <div class="col-12 col-lg-8">
    <header class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 mb-0"><?php post_type_archive_title(); ?></h1>
      <?php
      $total = isset($GLOBALS['wp_query']->found_posts) ? (int) $GLOBALS['wp_query']->found_posts : 0;
      if ( $total ) {
        printf(
          '<span class="text-muted small">%s</span>',
          sprintf( _n( '%s game', '%s games', $total, 'bst' ), number_format_i18n( $total ) )
        );
      }
      ?>
    </header>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      $gid   = get_the_ID();

      // კატეგორიების ბმულები
      $cats = get_the_category( $gid );
      $cat_links = [];
      if ( $cats ) {
        foreach ( $cats as $cat ) {
          $cat_links[] = '<a href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
        }
      }
    ?>
      <article <?php post_class('border rounded p-3 mb-3'); ?>>

        <div class="d-flex gap-3 align-items-start">

          <!-- Thumbnail -->
          <a class="flex-shrink-0" href="<?php the_permalink(); ?>">
            <?php echo get_the_post_thumbnail( $gid, 'thumbnail', [
              'class' => 'rounded',
              'style' => 'width:72px;height:72px;object-fit:cover;',
              'alt'   => esc_attr( get_the_title() ),
            ] ); ?>
          </a>

          <!-- Main -->
          <div class="flex-grow-1">
            <h2 class="h6 mb-1"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

            <?php if ( $cat_links ) : ?>
              <div class="small text-muted mb-2">
                <?php echo implode( ' • ', $cat_links ); ?>
              </div>
            <?php endif; ?>

            <div class="mb-2"><?php the_excerpt(); ?></div>
          </div>

          <!-- Action -->
          <div class="text-end">
            <a class="btn btn-outline-primary btn-sm" href="<?php the_permalink(); ?>">
              <?php _e('View game', 'bst'); ?>
            </a>
          </div>

        </div>
      </article>
    <?php endwhile; ?>

      <?php the_posts_pagination( ['mid_size' => 2] ); ?>

    <?php else : ?>
      <div class="alert alert-warning"><?php _e('No games.', 'bst'); ?></div>
    <?php endif; ?>
  </div>

  <?php get_sidebar(); ?>
<?php get_footer(); ?>