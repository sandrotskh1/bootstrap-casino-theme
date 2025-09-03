<?php get_header(); ?>
  <div class="col-12 col-lg-8">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article <?php post_class('mb-4'); ?>>
        <h1 class="h3 mb-3"><?php the_title(); ?></h1>
        <div><?php the_content(); ?></div>
      </article>
    <?php endwhile; endif; ?>
  </div>
  <?php get_sidebar(); ?>
<?php get_footer(); ?>