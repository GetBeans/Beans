<?php
/**
 * Echo the structural markup that wraps around comments. It also calls the comments action hooks.
 *
 * This template will return empty if the post which is called is password protected.
 *
 * @package Structure\Comments
 */

// Stop here if the post is password protected.
if ( post_password_required() ) {
	return;
}

beans_open_markup_e( 'beans_comments', 'div', array( 'id' => 'comments', 'class' => 'tm-comments' . ( current_theme_supports( 'beans-default-styling' ) ? ' uk-panel-box' : null ) ) );

	if ( comments_open() || get_comments_number() ) :

		if ( have_comments() ) :

			beans_open_markup_e( 'beans_comments_list', 'ol', array( 'class' => 'uk-comment-list' ) );

				wp_list_comments( array(
					'avatar_size' => 50,
					'callback'    => 'beans_comment_callback',
				) );

			beans_close_markup_e( 'beans_comments_list', 'ol' );

		else :

			/**
			 * Fires if no comments exist.
			 *
			 * This hook only fires if comments are open.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_no_comment' );

		endif;

		/**
		 * Fires after the comments list.
		 *
		 * This hook only fires if comments are open.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_after_open_comments' );

	endif;

	if ( ! comments_open() ) :

		/**
		 * Fires if comments are closed.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_comments_closed' );

	endif;

beans_close_markup_e( 'beans_comments', 'div' );
