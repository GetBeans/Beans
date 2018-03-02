<?php
/**
 * Loads Beans fragments.
 *
 * @package Beans\Framework\Render
 *
 * @since   1.0.0
 */

// Filter.
beans_add_smart_action( 'template_redirect', 'beans_load_global_fragments', 1 );
/**
 * Load global fragments and dynamic views.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_load_global_fragments() {
	beans_load_fragment_file( 'breadcrumb' );
	beans_load_fragment_file( 'footer' );
	beans_load_fragment_file( 'header' );
	beans_load_fragment_file( 'menu' );
	beans_load_fragment_file( 'post-shortcodes' );
	beans_load_fragment_file( 'post' );
	beans_load_fragment_file( 'widget-area' );
	beans_load_fragment_file( 'embed' );
	beans_load_fragment_file( 'deprecated' );
}

// Filter.
beans_add_smart_action( 'comments_template', 'beans_load_comments_fragment' );
/**
 * Load comments fragments.
 *
 * The comments fragments only loads if comments are active to prevent unnecessary memory usage.
 *
 * @since 1.0.0
 *
 * @param string $template The template filename.
 *
 * @return string The template filename.
 */
function beans_load_comments_fragment( $template ) {

	if ( empty( $template ) ) {
		return;
	}

	beans_load_fragment_file( 'comments' );

	return $template;
}

beans_add_smart_action( 'dynamic_sidebar_before', 'beans_load_widget_fragment', -1 );
/**
 * Load widget fragments.
 *
 * The widget fragments only loads if a sidebar is active to prevent unnecessary memory usage.
 *
 * @since 1.0.0
 *
 * @return bool True on success, false on failure.
 */
function beans_load_widget_fragment() {
	return beans_load_fragment_file( 'widget' );
}

beans_add_smart_action( 'pre_get_search_form', 'beans_load_search_form_fragment' );
/**
 * Load search form fragments.
 *
 * The search form fragments only loads if search is active to prevent unnecessary memory usage.
 *
 * @since 1.0.0
 *
 * @return bool True on success, false on failure.
 */
function beans_load_search_form_fragment() {
	return beans_load_fragment_file( 'searchform' );
}
