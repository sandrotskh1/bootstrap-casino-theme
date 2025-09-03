<?php if ( ! defined( 'ABSPATH' ) ) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<a class="visually-hidden-focusable position-absolute top-0 start-0 m-2 p-2 bg-light rounded" href="#content">
  <?php _e('Skip to content', 'bst'); ?>
</a>

<?php
// active classes for right-side links
$games_active   = ( is_singular('game') || is_post_type_archive('game') ) ? 'active' : '';
$casinos_active = ( is_singular('casino') || is_post_type_archive('casino') ) ? 'active' : '';
?>

<header class="site-header-dark sticky-top position-relative">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">

      <!-- Brand (logo or site name) -->
      <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="<?php echo esc_url( home_url('/') ); ?>">
        <?php
        if ( function_exists('the_custom_logo') && has_custom_logo() ) {
          the_custom_logo();
        } else {
          bloginfo('name');
        }
        ?>
      </a>

      <!-- Mobile toggler -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav"
              aria-controls="primaryNav" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation','bst'); ?>">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Right-aligned simple menu: Games / Casinos -->
      <div class="collapse navbar-collapse" id="primaryNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?php echo esc_attr($games_active); ?>" href="<?php echo esc_url( get_post_type_archive_link('game') ); ?>">
              <?php _e('Games', 'bst'); ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo esc_attr($casinos_active); ?>" href="<?php echo esc_url( get_post_type_archive_link('casino') ); ?>">
              <?php _e('Casinos', 'bst'); ?>
            </a>
          </li>
        </ul>
      </div>

    </div><!-- /.container -->
  </nav>
</header>

<main id="content" class="container my-4">
  <div class="row g-4">