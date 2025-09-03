<?php get_header(); ?>
  <div class="col-12 col-lg-8">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article <?php post_class('mb-4'); ?>>
        <h2 class="h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="text-muted small mb-2"><?php the_time( get_option('date_format') ); ?></div>
        <div><?php the_excerpt(); ?></div>
      </article>
    <?php endwhile; the_posts_pagination(); else: ?>
      <div class="alert alert-warning"><?php _e("No posts found.", "bst"); ?></div>
    <?php endif; ?>
  </div>
  <?php get_sidebar(); ?>
<?php get_footer(); ?>