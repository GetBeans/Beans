<?php
/**
 * Echo the structural markup for the main content. It also calls the content action hooks.
 *
 * @package Beans\Framework\Templates\Structure
 *
 * @since 1.0.0
 */

$content_attributes = array( // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.
	'class'    => 'tm-content',
	'role'     => 'main',
	'itemprop' => 'mainEntityOfPage',
);

// Blog specific attributes.
if ( is_home() || is_page_template( 'page_blog.php' ) || is_singular( 'post' ) || is_archive() ) {

	$content_attributes['itemscope'] = 'itemscope'; // Automatically escaped. @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.
	$content_attributes['itemtype']  = 'http://schema.org/Blog'; // Automatically escaped. @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.

}

// Blog specific attributes.
if ( is_search() ) {

	$content_attributes['itemscope'] = 'itemscope'; // Automatically escaped. @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.
	$content_attributes['itemtype']  = 'http://schema.org/SearchResultsPage'; // Automatically escaped. @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound.

}

beans_open_markup_e( 'beans_content', 'div', $content_attributes );

	/**
	 * Fires in the main content.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_content' );

beans_close_markup_e( 'beans_content', 'div' );
