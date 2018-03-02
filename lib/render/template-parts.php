<?php
/**
 * Loads the Beans template parts.
 *
 * The template parts contain the structural markup and hooks to which the fragments are attached.
 *
 * @package Beans\Framework\Render
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_load_document', 'beans_header_template', 5 );
/**
 * Echo header template part.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_header_template() {
	get_header();
}

beans_add_smart_action( 'beans_site_prepend_markup', 'beans_header_partial_template' );
/**
 * Echo header partial template part.
 *
 * @since 1.3.0
 *
 * @return void
 */
function beans_header_partial_template() {

	// Allow overwrite.
	if ( '' !== locate_template( 'header-partial.php', true, false ) ) {
		return;
	}

	require BEANS_STRUCTURE_PATH . 'header-partial.php';
}

beans_add_smart_action( 'beans_load_document', 'beans_content_template' );
/**
 * Echo main content template part.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_content_template() {

	// Allow overwrite.
	if ( '' !== locate_template( 'content.php', true ) ) {
		return;
	}

	require_once BEANS_STRUCTURE_PATH . 'content.php';
}

beans_add_smart_action( 'beans_content', 'beans_loop_template' );
/**
 * Echo loop template part.
 *
 * @since 1.0.0
 *
 * @param string $id Optional. The loop ID is used to filter the loop WP_Query arguments.
 *
 * @return void
 */
function beans_loop_template( $id = false ) {

	// Set default loop id.
	if ( ! $id ) {
		$id = 'main';
	}

	// Only run new query if a filter is set.
	$_has_filter = beans_has_filters( "beans_loop_query_args[_{$id}]" );

	if ( $_has_filter ) {
		global $wp_query;

		/**
		 * Filter the beans loop query. This can be used for custom queries.
		 *
		 * @since 1.0.0
		 */
		$args     = beans_apply_filters( "beans_loop_query_args[_{$id}]", false );
		$wp_query = new WP_Query( $args ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Used inside a function scope.
	}

	// Allow overwrite. Require the default loop.php if no overwrite is found.
	if ( '' === locate_template( 'loop.php', true, false ) ) {
		require BEANS_STRUCTURE_PATH . 'loop.php';
	}

	// Only reset the query if a filter is set.
	if ( $_has_filter ) {
		wp_reset_query(); // // phpcs:ignore WordPress.WP.DiscouragedFunctions.wp_reset_query_wp_reset_query -- Ensure the main query has been reset to the original main query.
	}
}

beans_add_smart_action( 'beans_post_after_markup', 'beans_comments_template', 15 );
/**
 * Echo comments template part.
 *
 * The comments template part only loads if comments are active to prevent unnecessary memory usage.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_comments_template() {
	global $post;

	$shortcircuit_conditions = array(
		beans_get( 'ID', $post ) && ! ( comments_open() || get_comments_number() ),
		! post_type_supports( beans_get( 'post_type', $post ), 'comments' ),
	);

	if ( in_array( true, $shortcircuit_conditions, true ) ) {
		return;
	}

	comments_template();
}

beans_add_smart_action( 'beans_comment', 'beans_comment_template' );
/**
 * Echo comment template part.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_comment_template() {

	// Allow overwrite.
	if ( '' !== locate_template( 'comment.php', true, false ) ) {
		return;
	}

	require BEANS_STRUCTURE_PATH . 'comment.php';
}

beans_add_smart_action( 'beans_widget_area', 'beans_widget_area_template' );
/**
 * Echo widget area template part.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_area_template() {

	// Allow overwrite.
	if ( '' !== locate_template( 'widget-area.php', true, false ) ) {
		return;
	}

	require BEANS_STRUCTURE_PATH . 'widget-area.php';
}

beans_add_smart_action( 'beans_primary_after_markup', 'beans_sidebar_primary_template' );
/**
 * Echo primary sidebar template part.
 *
 * The primary sidebar template part only loads if the layout set includes it. This prevents unnecessary memory usage.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_sidebar_primary_template() {

	if ( false === stripos( beans_get_layout(), 'sp' ) || ! beans_has_widget_area( 'sidebar_primary' ) ) {
		return;
	}

	get_sidebar( 'primary' );
}

beans_add_smart_action( 'beans_primary_after_markup', 'beans_sidebar_secondary_template' );
/**
 * Echo secondary sidebar template part.
 *
 * The secondary sidebar template part only loads if the layout set includes it. This prevents unnecessary memory usage.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_sidebar_secondary_template() {

	if ( false === stripos( beans_get_layout(), 'ss' ) || ! beans_has_widget_area( 'sidebar_secondary' ) ) {
		return;
	}

	get_sidebar( 'secondary' );
}

beans_add_smart_action( 'beans_site_append_markup', 'beans_footer_partial_template' );
/**
 * Echo footer partial template part.
 *
 * @since 1.3.0
 *
 * @return void
 */
function beans_footer_partial_template() {

	// Allow overwrite.
	if ( '' !== locate_template( 'footer-partial.php', true, false ) ) {
		return;
	}

	require BEANS_STRUCTURE_PATH . 'footer-partial.php';
}

beans_add_smart_action( 'beans_load_document', 'beans_footer_template' );
/**
 * Echo footer template part.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_footer_template() {
	get_footer();
}

/**
 * Set the content width based on the Beans default layout.
 *
 * This is mainly added to align to WordPress.org requirements.
 *
 * @since 1.2.0
 *
 * @ignore
 * @access private
 */
if ( ! isset( $content_width ) ) {
	$content_width = 800; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Valid use case.
}
