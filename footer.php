<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
  </div><!-- /.row -->
</main>

<footer class="site-footer-dark mt-4">
  <!-- Footer Top -->
  <div class="footer-top py-5">
    <div class="container">
      <div class="row g-4">
        <!-- Brand / About -->
        <div class="col-md-4">
          <a class="navbar-brand text-white fw-semibold d-inline-flex align-items-center gap-2 mb-2" href="<?php echo esc_url( home_url('/') ); ?>">
            <?php
            if ( function_exists('the_custom_logo') && has_custom_logo() ) {
              the_custom_logo();
            } else {
              bloginfo('name');
            }
            ?>
          </a>
          <p class="text-white-50 mb-0">
            <?php echo esc_html( get_bloginfo('description') ); ?>
          </p>
        </div>

        <!-- Footer Menu (assign via Appearance → Menus) -->
        <div class="col-md-4">
          <h2 class="h6 text-white-50 mb-3"><?php _e('', 'bst'); ?></h2>
          <?php
          if ( has_nav_menu( 'footer' ) ) {
            wp_nav_menu( [
              'theme_location' => 'footer',
              'container'      => false,
              'menu_class'     => 'list-unstyled mb-0 footer-links',
              'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
              'fallback_cb'    => false,
              'depth'          => 1,
            ] );
          } else {
            
          }
          ?>
        </div>

        <!-- Footer Widget Area -->
        <div class="col-md-4">
          <h2 class="h6 text-white-50 mb-3"><?php _e('', 'bst'); ?></h2>
          <?php if ( is_active_sidebar( 'footer-1' ) ) {
            dynamic_sidebar( 'footer-1' );
          } else {
            
          } ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="footer-bottom py-3 border-top border-light-subtle">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
      <div class="text-white-50 small">
        &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.
        <?php if ( function_exists( 'the_privacy_policy_link' ) ) {
          the_privacy_policy_link( '<span class="px-2">•</span>', '' );
        } ?>
      </div>

      <div class="d-flex align-items-center gap-3 small">
        <a class="link-light link-underline-opacity-0 link-underline-opacity-75-hover" href="<?php echo esc_url( get_post_type_archive_link('game') ); ?>">
          <?php _e('Games', 'bst'); ?>
        </a>
        <a class="link-light link-underline-opacity-0 link-underline-opacity-75-hover" href="<?php echo esc_url( get_post_type_archive_link('casino') ); ?>">
          <?php _e('Casinos', 'bst'); ?>
        </a>
        <a href="#" class="btn btn-outline-light btn-sm" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">
          <?php _e('Back to top', 'bst'); ?>
        </a>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>