<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<aside class="col-12 col-lg-4">
  <?php if ( is_active_sidebar( 'primary-sidebar' ) ) {
    dynamic_sidebar( 'primary-sidebar' );
  } else { ?>
    <div class="alert alert-info m-0"><?php _e('Add widgets to the "Primary Sidebar".', 'bst'); ?></div>
  <?php } ?>
</aside>