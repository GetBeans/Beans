<?php
/**
 * Prepare and initialize the Beans framework.
 *
 * @package Initialize
 */

add_action( 'beans_init', 'beans_define_constants', -1 );
/**
 * Define constants.
 *
 * @ignore
 */
function beans_define_constants() {

	// Define version.
	define( 'BEANS_VERSION', '1.4.1' );

	// Define paths.
	if ( ! defined( 'BEANS_THEME_PATH' ) ) {
		define( 'BEANS_THEME_PATH', wp_normalize_path( trailingslashit( get_template_directory() ) ) );
	}

	define( 'BEANS_PATH', BEANS_THEME_PATH . 'lib/' );
	define( 'BEANS_API_PATH', BEANS_PATH . 'api/' );
	define( 'BEANS_ASSETS_PATH', BEANS_PATH . 'assets/' );
	define( 'BEANS_LANGUAGES_PATH', BEANS_PATH . 'languages/' );
	define( 'BEANS_RENDER_PATH', BEANS_PATH . 'render/' );
	define( 'BEANS_TEMPLATES_PATH', BEANS_PATH . 'templates/' );
	define( 'BEANS_STRUCTURE_PATH', BEANS_TEMPLATES_PATH . 'structure/' );
	define( 'BEANS_FRAGMENTS_PATH', BEANS_TEMPLATES_PATH . 'fragments/' );

	// Define urls.
	if ( ! defined( 'BEANS_THEME_URL' ) ) {
		define( 'BEANS_THEME_URL', trailingslashit( get_template_directory_uri() ) );
	}

	define( 'BEANS_URL', BEANS_THEME_URL . 'lib/' );
	define( 'BEANS_API_URL', BEANS_URL . 'api/' );
	define( 'BEANS_ASSETS_URL', BEANS_URL . 'assets/' );
	define( 'BEANS_LESS_URL', BEANS_ASSETS_URL . 'less/' );
	define( 'BEANS_JS_URL', BEANS_ASSETS_URL . 'js/' );
	define( 'BEANS_IMAGE_URL', BEANS_ASSETS_URL . 'images/' );

	// Define admin paths.
	define( 'BEANS_ADMIN_PATH', BEANS_PATH . 'admin/' );

	// Define admin url.
	define( 'BEANS_ADMIN_URL', BEANS_URL . 'admin/' );
	define( 'BEANS_ADMIN_ASSETS_URL', BEANS_ADMIN_URL . 'assets/' );
	define( 'BEANS_ADMIN_JS_URL', BEANS_ADMIN_ASSETS_URL . 'js/' );

}

add_action( 'beans_init', 'beans_load_dependencies', -1 );
/**
 * Load dependencies.
 *
 * @ignore
 */
function beans_load_dependencies() {

	require_once( BEANS_API_PATH . 'init.php' );

	// Load the necessary Beans components.
	beans_load_api_components( array(
		'actions',
		'html',
		'term-meta',
		'post-meta',
		'image',
		'wp-customize',
		'compiler',
		'uikit',
		'template',
		'layout',
		'widget',
	) );

	// Add third party styles and scripts compiler support.
	beans_add_api_component_support( 'wp_styles_compiler' );
	beans_add_api_component_support( 'wp_scripts_compiler' );

	/**
	 * Fires after Beans API loads.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_after_load_api' );

}

add_action( 'beans_init', 'beans_add_theme_support' );
/**
 * Add theme support.
 *
 * @ignore
 */
function beans_add_theme_support() {

	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-background' );
	add_theme_support( 'menus' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
	add_theme_support( 'custom-header', array(
		'width'       => 2000,
		'height'      => 500,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => false,
	) );

	// Beans specific.
	add_theme_support( 'offcanvas-menu' );
	add_theme_support( 'beans-default-styling' );

}

add_action( 'beans_init', 'beans_includes' );
/**
 * Include framework files.
 *
 * @ignore
 */
function beans_includes() {

	// Include admin.
	if ( is_admin() ) {

		require_once( BEANS_ADMIN_PATH . 'options.php' );
		require_once( BEANS_ADMIN_PATH . 'updater.php' );

	}

	// Include assets.
	require_once( BEANS_ASSETS_PATH . 'assets.php' );

	// Include customizer.
	if ( is_customize_preview() ) {
		require_once( BEANS_ADMIN_PATH . 'wp-customize.php' );
	}

	// Include renderers.
	require_once( BEANS_RENDER_PATH . 'template-parts.php' );
	require_once( BEANS_RENDER_PATH . 'fragments.php' );
	require_once( BEANS_RENDER_PATH . 'widget-area.php' );
	require_once( BEANS_RENDER_PATH . 'walker.php' );
	require_once( BEANS_RENDER_PATH . 'menu.php' );

}

add_action( 'beans_init', 'beans_load_textdomain' );
/**
 * Load text domain.
 *
 * @ignore
 */
function beans_load_textdomain() {

	load_theme_textdomain( 'tm-beans', BEANS_LANGUAGES_PATH );

}

/**
 * Fires before Beans loads.
 *
 * @since 1.0.0
 */
do_action( 'beans_before_init' );

	/**
	 * Load Beans framework.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_init' );

/**
 * Fires after Beans loads.
 *
 * @since 1.0.0
 */
do_action( 'beans_after_init' );
