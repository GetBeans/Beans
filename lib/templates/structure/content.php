<?php
/**
 * Echo the structural markup for the main content. It also calls the content action hooks.
 *
 * @package Beans\Framework\Templates\Structure
 *
 * @since   1.0.0
 * @since   1.5.0 Added ID and tabindex for skip links.
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variable called in a function scope.
$content_attributes = array(
	'class'    => 'tm-content',
	'id'       => 'beans-content',
	'role'     => 'main',
	'itemprop' => 'mainEntityOfPage',
	'tabindex' => '-1',
);

// Blog specific attributes.
if ( is_home() || is_page_template( 'page_blog.php' ) || is_singular( 'post' ) || is_archive() ) {

	$content_attributes['itemscope'] = 'itemscope'; // Automatically escaped.
	$content_attributes['itemtype']  = 'https://schema.org/Blog'; // Automatically escaped.

}

// Blog specific attributes.
if ( is_search() ) {

	$content_attributes['itemscope'] = 'itemscope'; // Automatically escaped.
	$content_attributes['itemtype']  = 'https://schema.org/SearchResultsPage'; // Automatically escaped.

}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

beans_open_markup_e( 'beans_content', 'div', $content_attributes );

	/**
	 * Fires in the main content.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_content' );

beans_close_markup_e( 'beans_content', 'div' );
