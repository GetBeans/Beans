<?php
/**
 * Echo the structural markup for each comment. It also calls the comment action hooks.
 *
 * @package Structure\Comment
 */

echo beans_open_markup( 'beans_comment', 'article', array(
	'id' => 'div-comment-' . get_comment_ID(),
	'class' => 'uk-comment',
	'itemprop' => 'comment',
	'itemscope' => 'itemscope',
	'itemtype' => 'http://schema.org/UserComments'
) );

	echo beans_open_markup( 'beans_comment_header', 'header', array( 'class' => 'uk-comment-header' ) );

		/**
		 * Fires in the comment header.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_comment_header' );

	echo beans_close_markup( 'beans_comment_header', 'header' );

	echo beans_open_markup( 'beans_comment_body', 'div', array( 'class' => 'uk-comment-body', 'itemprop' => 'commentText' ) );

		/**
		 * Fires in the comment body.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_comment_content' );

	echo beans_close_markup( 'beans_comment_body', 'div' );

echo beans_close_markup( 'beans_comment', 'article' );