<?php
/**
 * Deprecated fragments.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

/**
 * Deprecated. Echo head title.
 *
 * This function is deprecated since it was replaced by the 'title-tag' theme support.
 *
 * @since 1.0.0
 * @deprecated 1.2.0
 *
 * @return void
 */
function beans_head_title() {
	_deprecated_function( __FUNCTION__, '1.2.0', 'wp_title()' );
	wp_title( '|', true, 'right' );
}

/**
 * Deprecated. Modify head wp title.
 *
 * This function is deprecated since it was replaced by the 'title-tag' theme support.
 *
 * @since 1.0.0
 * @deprecated 1.2.0
 *
 * @param string $title The WordPress default title.
 * @param string $sep The title separator.
 *
 * @return string The modified title.
 */
function beans_wp_title( $title, $sep ) {
	_deprecated_function( __FUNCTION__, '1.2.0', 'wp_title()' );
	global $page, $paged;

	if ( is_feed() ) {
		return $title;
	}

	// Add the blog name.
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );

	if ( $site_desciption && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		// translators: Page number.
		$title .= " $sep " . sprintf( __( 'Page %s', 'tm-beans' ), max( $paged, $page ) );
	}

	return $title;
}

/**
 * Deprecated shortcodes.
 *
 * We declare the shortcodes for backward compatibility purposes but they shouldn't be used for further development.
 *
 * @deprecated 1.2.0
 *
 * @ignore
 *
 * @return void
 */
global $shortcode_tags;

$shortcode_tags = array_merge( // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Deprecated function.
	$shortcode_tags, array(
		'beans_post_meta_date'       => 'beans_post_meta_date_shortcode',
		'beans_post_meta_author'     => 'beans_post_meta_author_shortcode',
		'beans_post_meta_comments'   => 'beans_post_meta_comments_shortcode',
		'beans_post_meta_tags'       => 'beans_post_meta_tags_shortcode',
		'beans_post_meta_categories' => 'beans_post_meta_categories_shortcode',
	)
);
