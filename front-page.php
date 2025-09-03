<?php get_header(); ?>
  <div class="col-12 col-lg-8">
    <!-- Hero (შეგიძლია შეცვალო Image 1-ის მიხედვით) -->
    <section class="p-4 p-md-5 bg-light rounded-3 shadow-sm">
      <h1 class="display-6 mb-2"><?php bloginfo('name'); ?></h1>
      <p class="lead text-muted mb-4"><?php bloginfo('description'); ?></p>
      <div class="d-flex gap-2">
        <a href="<?php echo esc_url( get_post_type_archive_link('casino') ); ?>" class="btn btn-primary btn-lg"><?php _e('Browse Casinos', 'bst'); ?></a>
        <a href="<?php echo esc_url( get_post_type_archive_link('game') ); ?>" class="btn btn-outline-secondary btn-lg"><?php _e('Explore Games', 'bst'); ?></a>
      </div>
    </section>

    <!-- Shortcode Template 2 (dropdown + AJAX) -->
    <section class="mt-4">
      <?php echo do_shortcode('[casinos title="Best Casino" template="2" second_col="loyalty"]'); ?>
    </section>
  </div>
  <?php get_sidebar(); ?>
<?php get_footer(); ?>