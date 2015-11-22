<?php
/**
 * Add post shortcodes.
 *
 * @package Fragments\Post_Shortcodes
 */

beans_add_smart_action( 'beans_post_meta_date', 'beans_post_meta_date_shortcode' );

/**
 * Echo post meta date shortcode.
 *
 * @since 1.0.0
 */
function beans_post_meta_date_shortcode() {

	echo beans_output( 'beans_post_meta_date_prefix', __( 'Posted on ', 'tm-beans' ) );

	echo beans_open_markup( 'beans_post_meta_date', 'time', array(
		'datetime' => get_the_time( 'c' ),
		'itemprop' => 'datePublished',
	) );

		echo beans_output( 'beans_post_meta_date_text', get_the_time( get_option( 'date_format' ) ) );

	echo beans_close_markup( 'beans_post_meta_date', 'time' );

}


beans_add_smart_action( 'beans_post_meta_author', 'beans_post_meta_author_shortcode' );

/**
 * Echo post meta author shortcode.
 *
 * @since 1.0.0
 */
function beans_post_meta_author_shortcode() {

	beans_output( 'beans_post_meta_author_prefix', __( 'By ', 'tm-beans' ) ) ;

	echo beans_open_markup( 'beans_post_meta_author', 'a', array(
		'href' => esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		'rel' => 'author',
		'itemprop' => 'author',
		'itemtype' => 'http://schema.org/Person'
	) );

		echo beans_output( 'beans_post_meta_author_text', get_the_author() );

	echo beans_close_markup( 'beans_post_meta_author', 'a' );

}


beans_add_smart_action( 'beans_post_meta_comments', 'beans_post_meta_comments_shortcode' );

/**
 * Echo post meta comments shortcode.
 *
 * @since 1.0.0
 */
function beans_post_meta_comments_shortcode() {

	global $post;

	if ( post_password_required() || !comments_open() )
		return;

	$comments_number = (int) get_comments_number( $post->ID );

	if ( $comments_number < 1 )
		$comment_text = beans_output( 'beans_post_meta_empty_comment_text', __( 'Leave a comment', 'tm-beans' ) );
	else if ( $comments_number === 1 )
		$comment_text = beans_output( 'beans_post_meta_comments_text_singular', __( '1 comment', 'tm-beans' ) );
	else
		$comment_text = beans_output( 'beans_post_meta_comments_text_plurial', __( '%s comments', 'tm-beans' ) );

	echo beans_open_markup( 'beans_post_meta_comments', 'a', array(
		'href' => esc_url( get_comments_link() )
	) );

		printf( $comment_text, (int) get_comments_number( $post->ID ) );

	echo beans_close_markup( 'beans_post_meta_comments', 'a' );

}


beans_add_smart_action( 'beans_post_meta_tags', 'beans_post_meta_tags_shortcode' );

/**
 * Echo post meta tags shortcode.
 *
 * @since 1.0.0
 */
function beans_post_meta_tags_shortcode() {

	$tags = get_the_tag_list( null, ', ' );

	if ( !$tags || is_wp_error( $tags ) )
		return;

	echo beans_output( 'beans_post_meta_tags_prefix', __( 'Tagged with: ', 'tm-beans' ) ) . $tags;


}


beans_add_smart_action( 'beans_post_meta_categories', 'beans_post_meta_categories_shortcode' );

/**
 * Echo post meta categories shortcode.
 *
 * @since 1.0.0
 */
function beans_post_meta_categories_shortcode() {

	$categories = get_the_category_list( ', ' );

	if ( !$categories || is_wp_error( $categories ) )
		return;

	echo beans_output( 'beans_post_meta_categories_prefix', __( 'Filed under: ', 'tm-beans' ) ) . $categories;

}
