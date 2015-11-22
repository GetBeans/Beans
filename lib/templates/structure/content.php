<?php
/**
 * Echo the structural markup for the main content. It also calls the content action hooks.
 *
 * @package Structure\Content
 */

$content_attributes = array(
	'class' => 'tm-content',
	'role' => 'main',
	'itemprop' => 'mainContentOfPage'
);

// Blog specific attributes.
if ( is_home() || is_page_template( 'page_blog.php' ) || is_singular( 'post' ) || is_archive() ) {

	$content_attributes['itemscope'] = 'itemscope';
	$content_attributes['itemtype']  = 'http://schema.org/Blog';

}

// Blog specific attributes.
if ( is_search() ) {

	$content_attributes['itemscope'] = 'itemscope';
	$content_attributes['itemtype'] = 'http://schema.org/SearchResultsPage';

}

echo beans_open_markup( 'beans_content', 'div', $content_attributes );

	/**
	 * Fires in the main content.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_content' );

echo beans_close_markup( 'beans_content', 'div' );