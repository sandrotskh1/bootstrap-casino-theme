<?php
/**
 * Bootstrap Casino Theme
 * @package bst
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BST_VERSION', '1.0.1' );
define( 'BST_PATH', trailingslashit( get_template_directory() ) );
define( 'BST_URI',  trailingslashit( get_template_directory_uri() ) );

/** Safe require helper */
function bst_require_if_exists( $relpath ) {
    $path = BST_PATH . ltrim( $relpath, '/' );
    if ( file_exists( $path ) ) require_once $path;
}

/** Includes */
bst_require_if_exists( 'inc/helpers.php' );
bst_require_if_exists( 'inc/class-cpt-game.php' );
bst_require_if_exists( 'inc/class-cpt-casino.php' );
bst_require_if_exists( 'inc/class-widget-tabs.php' );
bst_require_if_exists( 'inc/class-shortcode-casinos.php' );
bst_require_if_exists( 'inc/class-ajax.php' );
bst_require_if_exists( 'inc/class-widget-read-next.php' );
bst_require_if_exists( 'inc/class-widget-post-tabs.php' );

add_action( 'after_setup_theme', function() {
    load_theme_textdomain( 'bst', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );

    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'bst' ),
        'footer'  => __( 'Footer Menu',  'bst' ),
    ] );
} );

add_action( 'widgets_init', function() {
    register_sidebar( [
        'name'          => __( 'Primary Sidebar', 'bst' ),
        'id'            => 'primary-sidebar',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title h5 mb-3">',
        'after_title'   => '</h2>',
    ] );

    register_sidebar( [
        'name'          => __( 'Footer Widgets', 'bst' ),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-3">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="h6 text-white mb-2">',
        'after_title'   => '</h3>',
    ] );

    if ( class_exists( 'BST_Widget_Tabs' ) )         register_widget( 'BST_Widget_Tabs' );
    if ( class_exists( 'BST_Widget_Read_Next' ) )    register_widget( 'BST_Widget_Read_Next' );
    if ( class_exists( 'BST_Widget_Post_Tabs' ) )    register_widget( 'BST_Widget_Post_Tabs' );
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3' );
    wp_enqueue_style( 'bst-theme', BST_URI . 'assets/css/theme.css', [ 'bootstrap' ], BST_VERSION );
    wp_enqueue_style( 'bst-style', get_stylesheet_uri(), [ 'bst-theme' ], BST_VERSION );

    wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true );
    wp_enqueue_script( 'bst-main', BST_URI . 'assets/js/main.js', [ 'jquery' ], BST_VERSION, true );
    wp_localize_script( 'bst-main', 'bstAjax', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'bst_nonce' ),
    ] );
} );

/** Register CPTs + Shortcodes */
add_action( 'init', function() {
    if ( class_exists( '\BST\CPT_Game' ) )           \BST\CPT_Game::register();
    if ( class_exists( '\BST\CPT_Casino' ) )         \BST\CPT_Casino::register();
    if ( class_exists( '\BST\Shortcode_Casinos' ) )  \BST\Shortcode_Casinos::register();
} );

/** Admin-only bits (meta boxes) */
add_action( 'admin_init', function() {
    if ( class_exists( '\BST\CPT_Game' ) )           \BST\CPT_Game::admin();
    if ( class_exists( '\BST\CPT_Casino' ) )         \BST\CPT_Casino::admin();
} );

/** REST/Gutenberg-safe save handlers (critical) */
add_action( 'save_post_game',   [ '\BST\CPT_Game',   'save' ] );
add_action( 'save_post_casino', [ '\BST\CPT_Casino', 'save' ] );

/** AJAX */
if ( class_exists( '\BST\BST_Ajax' ) ) \BST\BST_Ajax::register();