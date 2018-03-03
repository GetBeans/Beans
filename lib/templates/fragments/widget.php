<?php
/**
 * Echo widget fragments.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_widget', 'beans_widget_badge', 5 );
/**
 * Echo widget badge.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_badge() {

	if ( ! beans_get_widget( 'badge' ) ) {
		return;
	}

	beans_open_markup_e( 'beans_widget_badge' . _beans_widget_subfilters(), 'div', 'class=uk-panel-badge uk-badge' );

		echo beans_widget_shortcodes( beans_get_widget( 'badge_content' ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.

	beans_close_markup_e( 'beans_widget_badge' . _beans_widget_subfilters(), 'div' );
}

beans_add_smart_action( 'beans_widget', 'beans_widget_title' );
/**
 * Echo widget title.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_title() {
	$title = beans_get_widget( 'title' );

	if ( ! $title || ! beans_get_widget( 'show_title' ) ) {
		return;
	}

	beans_open_markup_e( 'beans_widget_title' . _beans_widget_subfilters(), 'h3', 'class=uk-panel-title' );

		beans_output_e( 'beans_widget_title_text', $title );

	beans_close_markup_e( 'beans_widget_title' . _beans_widget_subfilters(), 'h3' );
}

beans_add_smart_action( 'beans_widget', 'beans_widget_content', 15 );
/**
 * Echo widget content.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_content() {
	beans_open_markup_e( 'beans_widget_content' . _beans_widget_subfilters(), 'div' );

		beans_output_e( 'beans_widget_content' . _beans_widget_subfilters(), beans_get_widget( 'content' ) );

	beans_close_markup_e( 'beans_widget_content' . _beans_widget_subfilters(), 'div' );
}

beans_add_smart_action( 'beans_no_widget', 'beans_no_widget' );
/**
 * Echo no widget content.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_no_widget() {

	// Only apply this notice to sidebar_primary and sidebar_secondary.
	if ( ! in_array( beans_get_widget_area( 'id' ), array( 'sidebar_primary', 'sidebar_secondary' ), true ) ) {
		return;
	}

	beans_open_markup_e( 'beans_no_widget_notice', 'p', array( 'class' => 'uk-alert uk-alert-warning' ) );

		beans_output_e(
			'beans_no_widget_notice_text',
			// translators: Name of the widget area.
			sprintf( __( '%s does not have any widget assigned!', 'tm-beans' ), beans_get_widget_area( 'name' ) )
		);

	beans_close_markup_e( 'beans_no_widget_notice', 'p' );
}

beans_add_filter( 'beans_widget_content_rss_output', 'beans_widget_rss_content' );
/**
 * Modify RSS widget content.
 *
 * @since 1.0.0
 *
 * @return string The RSS widget content.
 */
function beans_widget_rss_content() {

	$options = beans_get_widget( 'options' );

	return '<p><a class="uk-button" href="' . beans_get( 'url', $options ) . '" target="_blank">' . __( 'Read feed', 'tm-beans' ) . '</a><p>';
}

beans_add_filter( 'beans_widget_content_attributes', 'beans_modify_widget_content_attributes' );
/**
 * Modify core widgets content attributes, so they use the default UIKit styling.
 *
 * @since 1.0.0
 *
 * @param array $attributes The current widget attributes.
 *
 * @return array The modified widget attributes.
 */
function beans_modify_widget_content_attributes( $attributes ) {
	$type = beans_get_widget( 'type' );

	$target = array(
		'archives',
		'categories',
		'links',
		'meta',
		'pages',
		'recent-posts',
		'recent-comments',
	);

	$current_class = isset( $attributes['class'] ) ? $attributes['class'] . ' ' : '';

	if ( in_array( beans_get_widget( 'type' ), $target, true ) ) {
		$attributes['class'] = $current_class . 'uk-list'; // Automatically escaped.
	}

	if ( 'calendar' === $type ) {
		$attributes['class'] = $current_class . 'uk-table uk-table-condensed'; // Automatically escaped.
	}

	return $attributes;
}

beans_add_filter( 'beans_widget_content_categories_output', 'beans_modify_widget_count' );
beans_add_filter( 'beans_widget_content_archives_output', 'beans_modify_widget_count' );
/**
 * Modify widget count.
 *
 * @since 1.0.0
 *
 * @param string $content The widget content.
 *
 * @return string The modified widget content.
 */
function beans_modify_widget_count( $content ) {
	$count = beans_output( 'beans_widget_count', '$1' );

	if ( true === beans_get( 'dropdown', beans_get_widget( 'options' ) ) ) {
		$output = $count;
	} else {
		$output  = beans_open_markup( 'beans_widget_count', 'span', 'class=tm-count' );
		$output .= $count;
		$output .= beans_close_markup( 'beans_widget_count', 'span' );
	}

	// Keep closing tag to avoid overwriting the inline JavaScript.
	return preg_replace( '#>((\s|&nbsp;)\((.*)\))#', '>' . $output, $content );
}

beans_add_filter( 'beans_widget_content_categories_output', 'beans_remove_widget_dropdown_label' );
beans_add_filter( 'beans_widget_content_archives_output', 'beans_remove_widget_dropdown_label' );
/**
 * Modify widget dropdown label.
 *
 * @since 1.0.0
 *
 * @param string $content The widget content.
 *
 * @return string The modified widget content.
 */
function beans_remove_widget_dropdown_label( $content ) {
	return preg_replace( '#<label([^>]*)class="screen-reader-text"(.*?)>(.*?)</label>#', '', $content );
}
