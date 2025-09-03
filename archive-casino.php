<?php
// wp-content/themes/bootstrap-casino-theme/archive-casino.php
if ( ! defined('ABSPATH') ) exit;
get_header(); ?>

  <div class="col-12 col-lg-8">
    <header class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 mb-0"><?php post_type_archive_title(); ?></h1>
    </header>

    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post();
        $cid      = get_the_ID();
        $official = \BST\Helpers::get_casino_meta( $cid, 'official_site' );
        $year     = \BST\Helpers::get_casino_meta( $cid, 'year_est' );
        $rating   = \BST\Helpers::get_casino_meta( $cid, 'rating_overall' );
        $loyalty  = \BST\Helpers::get_casino_meta( $cid, 'loyalty', 0 );
        $live     = \BST\Helpers::get_casino_meta( $cid, 'live_casino', 0 );
        $mobile   = \BST\Helpers::get_casino_meta( $cid, 'mobile_casino', 0 );
        $games    = \BST\Helpers::get_casino_games( $cid );
        $games_preview = array_slice( (array) $games, 0, 3 );
      ?>
      <article <?php post_class('border rounded p-3 mb-3'); ?>>

        <div class="d-flex gap-3 align-items-start">

          <!-- Thumbnail -->
          <a class="flex-shrink-0" href="<?php the_permalink(); ?>">
            <?php echo get_the_post_thumbnail( $cid, 'thumbnail', [
              'class' => 'rounded',
              'style' => 'width:72px;height:72px;object-fit:cover;',
              'alt'   => esc_attr( get_the_title() ),
            ] ); ?>
          </a>

          <!-- Main info -->
          <div class="flex-grow-1">
            <div class="d-flex align-items-start gap-2">
              <h2 class="h6 mb-1"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <?php if ( $rating !== '' ) : ?>
                <span class="badge bg-dark-subtle text-dark-emphasis ms-auto">
                  <?php echo esc_html( number_format_i18n( (float) $rating, 1 ) ); ?>
                </span>
              <?php endif; ?>
            </div>

            <!-- small details -->
            <div class="small text-muted mb-2">
              <?php
                $bits = [];
                if ( $year ) {
                  $bits[] = sprintf( esc_html__( 'Est. %s', 'bst' ), esc_html( $year ) );
                }
                if ( $official ) {
                  $host = parse_url( $official, PHP_URL_HOST ) ?: $official;
                  $bits[] = '<a href="'. esc_url( $official ) .'" target="_blank" rel="nofollow noopener">'. esc_html( $host ) .'</a>';
                }
                echo implode( ' &middot; ', $bits );
              ?>
            </div>

            <!-- feature badges -->
            <div class="d-flex flex-wrap gap-2 mb-2">
              <span class="badge <?php echo $loyalty ? 'bg-success' : 'bg-secondary'; ?>">
                <?php _e('Loyalty','bst'); ?>: <?php echo esc_html( \BST\Helpers::esc_bool_label( $loyalty ) ); ?>
              </span>
              <span class="badge <?php echo $live ? 'bg-success' : 'bg-secondary'; ?>">
                <?php _e('Live','bst'); ?>: <?php echo esc_html( \BST\Helpers::esc_bool_label( $live ) ); ?>
              </span>
              <span class="badge <?php echo $mobile ? 'bg-success' : 'bg-secondary'; ?>">
                <?php _e('Mobile','bst'); ?>: <?php echo esc_html( \BST\Helpers::esc_bool_label( $mobile ) ); ?>
              </span>
            </div>

            <!-- games preview -->
            <?php if ( $games_preview ) : ?>
              <div class="small">
                <span class="text-muted"><?php _e('Games:', 'bst'); ?></span>
                <?php
                  $links = [];
                  foreach ( $games_preview as $gid ) {
                    $links[] = '<a href="'. esc_url( get_permalink( $gid ) ) .'">'. esc_html( get_the_title( $gid ) ) .'</a>';
                  }
                  echo implode(', ', $links);
                  if ( count( $games ) > count( $games_preview ) ) echo '…';
                ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Action -->
          <div class="text-end">
            <?php
              $link   = $official ? $official : get_permalink( $cid );
              $target = $official ? ' target="_blank" rel="nofollow noopener"' : '';
              $label  = $official ? __( 'Visit', 'bst' ) : __( 'Review', 'bst' );
            ?>
            <a class="btn btn-primary btn-sm" href="<?php echo esc_url( $link ); ?>"<?php echo $target; ?>>
              <?php echo esc_html( $label ); ?>
            </a>
          </div>

        </div>
      </article>
      <?php endwhile; ?>

      <?php the_posts_pagination( ['mid_size'=>2] ); ?>

    <?php else : ?>
      <div class="alert alert-warning"><?php _e('No casinos.', 'bst'); ?></div>
    <?php endif; ?>
  </div>

  <?php get_sidebar(); ?>
<?php get_footer(); ?>