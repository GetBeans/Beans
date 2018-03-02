<?php
/**
 * Echo the structural markup for each comment. It also calls the comment action hooks.
 *
 * @package Structure\Comment
 */

beans_open_markup_e( 'beans_comment', 'article', array(
	'id'        => 'div-comment-' . get_comment_ID(), // Automatically escaped.
	'class'     => 'uk-comment',
	'itemprop'  => 'comment',
	'itemscope' => 'itemscope',
	'itemtype'  => 'http://schema.org/Comment',
) );

	beans_open_markup_e( 'beans_comment_header', 'header', array( 'class' => 'uk-comment-header' ) );

		/**
		 * Fires in the comment header.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_comment_header' );

	beans_close_markup_e( 'beans_comment_header', 'header' );

	beans_open_markup_e( 'beans_comment_body', 'div', array(
		'class'    => 'uk-comment-body',
		'itemprop' => 'text',
	) );

		/**
		 * Fires in the comment body.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_comment_content' );

	beans_close_markup_e( 'beans_comment_body', 'div' );

beans_close_markup_e( 'beans_comment', 'article' );
