<?php
/**
 * Add Beans assets.
 *
 * @package Beans\Framework\Assets
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_uikit_enqueue_scripts', 'beans_enqueue_uikit_components', 5 );
/**
 * Enqueue UIkit components and Beans style.
 *
 * Beans style is enqueued with the UIKit components to have access to UIKit LESS variables.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_enqueue_uikit_components() {
	$core = array(
		'base',
		'block',
		'grid',
		'article',
		'comment',
		'panel',
		'nav',
		'navbar',
		'subnav',
		'table',
		'breadcrumb',
		'pagination',
		'list',
		'form',
		'button',
		'badge',
		'alert',
		'dropdown',
		'offcanvas',
		'text',
		'utility',
		'icon',
	);

	beans_uikit_enqueue_components( $core, 'core', false );

	// Include UIkit default theme.
	beans_uikit_enqueue_theme( 'default' );

	// Enqueue UIkit overwrite theme folder.
	beans_uikit_enqueue_theme( 'beans', BEANS_ASSETS_PATH . 'less/uikit-overwrite' );

	// Add the theme style as a UIkit fragment to have access to all the variables.
	beans_compiler_add_fragment( 'uikit', BEANS_ASSETS_PATH . 'less/style.less', 'less' );

	// Add the theme default style as a UIkit fragment only if the theme supports it.
	if ( current_theme_supports( 'beans-default-styling' ) ) {
		beans_compiler_add_fragment( 'uikit', BEANS_ASSETS_PATH . 'less/default.less', 'less' );
	}
}

beans_add_smart_action( 'wp_enqueue_scripts', 'beans_enqueue_assets', 5 );
/**
 * Enqueue Beans assets.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_enqueue_assets() {

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

beans_add_smart_action( 'after_setup_theme', 'beans_add_editor_assets' );
/**
 * Add Beans editor assets.
 *
 * @since 1.2.5
 *
 * @return void
 */
function beans_add_editor_assets() {
	add_editor_style( BEANS_ASSETS_URL . 'css/editor' . BEANS_MIN_CSS . '.css' );
}
