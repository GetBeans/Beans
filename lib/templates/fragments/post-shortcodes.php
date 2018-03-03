<?php
/**
 * Add post shortcodes.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_post_meta_date', 'beans_post_meta_date_shortcode' );
/**
 * Echo post meta date shortcode.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_post_meta_date_shortcode() {
	beans_output_e( 'beans_post_meta_date_prefix', __( 'Posted on ', 'tm-beans' ) );

	beans_open_markup_e(
		'beans_post_meta_date',
		'time',
		array(
			'datetime' => get_the_time( 'c' ),
			'itemprop' => 'datePublished',
		)
	);

		beans_output_e( 'beans_post_meta_date_text', get_the_time( get_option( 'date_format' ) ) );

	beans_close_markup_e( 'beans_post_meta_date', 'time' );
}

beans_add_smart_action( 'beans_post_meta_author', 'beans_post_meta_author_shortcode' );
/**
 * Echo post meta author shortcode.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_post_meta_author_shortcode() {
	beans_output_e( 'beans_post_meta_author_prefix', __( 'By ', 'tm-beans' ) );

	beans_open_markup_e(
		'beans_post_meta_author',
		'a',
		array(
			'href'      => get_author_posts_url( get_the_author_meta( 'ID' ) ), // Automatically escaped.
			'rel'       => 'author',
			'itemprop'  => 'author',
			'itemscope' => '',
			'itemtype'  => 'http://schema.org/Person',
		)
	);

		beans_output_e( 'beans_post_meta_author_text', get_the_author() );

		beans_selfclose_markup_e(
			'beans_post_meta_author_name_meta',
			'meta',
			array(
				'itemprop' => 'name',
				'content'  => get_the_author(), // Automatically escaped.
			)
		);

	beans_close_markup_e( 'beans_post_meta_author', 'a' );
}

beans_add_smart_action( 'beans_post_meta_comments', 'beans_post_meta_comments_shortcode' );
/**
 * Echo post meta comments shortcode.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_post_meta_comments_shortcode() {
	global $post;

	if ( post_password_required() || ! comments_open() ) {
		return;
	}

	$comments_number = (int) get_comments_number( $post->ID );

	if ( $comments_number < 1 ) {
		$comment_text = beans_output( 'beans_post_meta_empty_comment_text', __( 'Leave a comment', 'tm-beans' ) );
	} elseif ( 1 === $comments_number ) {
		$comment_text = beans_output( 'beans_post_meta_comments_text_singular', __( '1 comment', 'tm-beans' ) );
	} else {
		$comment_text = beans_output(
			'beans_post_meta_comments_text_plural',
			// translators: Number of comments. Plural.
			__( '%s comments', 'tm-beans' )
		);
	}

	beans_open_markup_e( 'beans_post_meta_comments', 'a', array( 'href' => get_comments_link() ) ); // Automatically escaped.

		printf( $comment_text, (int) get_comments_number( $post->ID ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.

	beans_close_markup_e( 'beans_post_meta_comments', 'a' );
}

beans_add_smart_action( 'beans_post_meta_tags', 'beans_post_meta_tags_shortcode' );
/**
 * Echo post meta tags shortcode.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_post_meta_tags_shortcode() {

	$tags = get_the_tag_list( null, ', ' );

	if ( ! $tags || is_wp_error( $tags ) ) {
		return;
	}

	printf( '%1$s%2$s', beans_output( 'beans_post_meta_tags_prefix', __( 'Tagged with: ', 'tm-beans' ) ), $tags ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.
}

beans_add_smart_action( 'beans_post_meta_categories', 'beans_post_meta_categories_shortcode' );
/**
 * Echo post meta categories shortcode.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_post_meta_categories_shortcode() {
	$categories = get_the_category_list( ', ' );

	if ( ! $categories || is_wp_error( $categories ) ) {
		return;
	}

	printf( '%1$s%2$s', beans_output( 'beans_post_meta_categories_prefix', __( 'Filed under: ', 'tm-beans' ) ), $categories ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.
}
