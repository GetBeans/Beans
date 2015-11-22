<?php
/**
 * Echo comments fragments.
 *
 * @package Fragments\Comments
 */

beans_add_smart_action( 'beans_comments_list_before_markup', 'beans_comments_title' );

/**
 * Echo the comments title.
 *
 * @since 1.0.0
 */
function beans_comments_title() {

	echo beans_open_markup( 'beans_comments_title', 'h2' );

		echo beans_output( 'beans_comments_title_text', sprintf(
			_n( '%s Comment', '%s Comments', get_comments_number(), 'tm-beans' ),
			number_format_i18n( get_comments_number() )
		) );

	echo beans_close_markup( 'beans_comments_title', 'h2' );

}


beans_add_smart_action( 'beans_comment_header', 'beans_comment_avatar', 5 );

/**
 * Echo the comment avatar.
 *
 * @since 1.0.0
 */
function beans_comment_avatar() {

	global $comment;

	// Stop here if no avatar.
	if ( !$avatar = get_avatar( $comment, $comment->args['avatar_size'] ) )
		return;

	echo beans_open_markup( 'beans_comment_avatar', 'div', array( 'class' => 'uk-comment-avatar' ) );

		echo $avatar;

	echo beans_close_markup( 'beans_comment_avatar', 'div' );

}


beans_add_smart_action( 'beans_comment_header', 'beans_comment_author' );

/**
 * Echo the comment author title.
 *
 * @since 1.0.0
 */
function beans_comment_author() {

	echo beans_open_markup( 'beans_comment_title', 'div', array(
		'class' => 'uk-comment-title',
		'itemprop' => 'creator',
		'itemscope' => 'itemscope',
		'itemtype' => 'http://schema.org/Person'
	) );

		echo get_comment_author_link();

	echo beans_close_markup( 'beans_comment_title', 'div' );

}


beans_add_smart_action( 'beans_comment_title_append_markup', 'beans_comment_badges' );

/**
 * Echo the comment badges.
 *
 * @since 1.0.0
 */
function beans_comment_badges() {

	global $comment;

	// Trackback badge.
	if ( $comment->comment_type == 'trackback' ) {

		echo beans_open_markup( 'beans_trackback_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left' ) );

			echo beans_output( 'beans_trackback_text', __( 'Trackback', 'tm-beans' ) );

		echo beans_close_markup( 'beans_trackback_badge', 'span' );

	}

	// Pindback badge.
	if ( $comment->comment_type == 'pingback' ) {

		echo beans_open_markup( 'beans_pingback_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left' ) );

			echo beans_output( 'beans_pingback_text', __( 'Pingback', 'tm-beans' ) );

		echo beans_close_markup( 'beans_pingback_badge', 'span' );


	}

	// Moderation badge.
	if ( '0' == $comment->comment_approved ) {

		echo beans_open_markup( 'beans_moderation_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left uk-badge-warning' ) );

			echo beans_output( 'beans_moderation_text', __( 'Awaiting Moderation', 'tm-beans' ) );

		echo beans_close_markup( 'beans_moderation_badge', 'span' );


	}

	// Moderator badge.
	if ( user_can( $comment->user_id, 'moderate_comments' ) ) {

		echo beans_open_markup( 'beans_moderator_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left' ) );

			echo beans_output( 'beans_moderator_text', __( 'Moderator', 'tm-beans' ) );

		echo beans_close_markup( 'beans_moderator_badge', 'span' );


	}

}


beans_add_smart_action( 'beans_comment_header', 'beans_comment_metadata', 15 );

/**
 * Echo the comment metadata.
 *
 * @since 1.0.0
 */
function beans_comment_metadata() {

	echo beans_open_markup( 'beans_comment_meta', 'div', array( 'class' => 'uk-comment-meta' ) );

		echo beans_open_markup( 'beans_comment_time', 'time', array(
			'datetime' => get_comment_time( 'c' ),
			'itemprop' => 'commentTime'
		) );

			echo beans_output( 'beans_comment_time_text', sprintf(
				_x( '%1$s at %2$s', '1: date, 2: time', 'tm-beans' ),
				get_comment_date(),
				get_comment_time()
			) );

		echo beans_close_markup( 'beans_comment_time', 'time' );

	echo beans_close_markup( 'beans_comment_meta', 'div' );

}


beans_add_smart_action( 'beans_comment_content', 'beans_comment_content' );

/**
 * Echo the comment content.
 *
 * @since 1.0.0
 */
function beans_comment_content() {

	echo beans_output( 'beans_comment_content', beans_render_function( comment_text() ) );

}


beans_add_smart_action( 'beans_comment_content', 'beans_comment_links', 15 );

/**
 * Echo the comment links.
 *
 * @since 1.0.0
 */
function beans_comment_links() {

	global $comment;

	echo beans_open_markup( 'beans_comment_links', 'ul', array( 'class' => 'tm-comment-links uk-subnav uk-subnav-line' ) );

		// Reply.
		echo get_comment_reply_link( array_merge( $comment->args, array(
			'add_below' => 'comment-content',
			'depth' => $comment->depth,
			'max_depth' => $comment->args['max_depth'],
			'before' => beans_open_markup( 'beans_comment_item[_reply]', 'li' ),
			'after' => beans_close_markup( 'beans_comment_item[_reply]', 'li' )
		) ) );

		// Edit.
		if ( current_user_can( 'moderate_comments' ) ) :

			echo beans_open_markup( 'beans_comment_item[_edit]', 'li' );

				echo beans_open_markup( 'beans_comment_item_link[_edit]', 'a', array(
					'href' => esc_url( get_edit_comment_link( $comment->comment_ID ) )
				) );

					echo beans_output( 'beans_comment_edit_text', __( 'Edit', 'tm-beans' ) );

				echo beans_close_markup( 'beans_comment_item_link[_edit]', 'a' );

			echo beans_close_markup( 'beans_comment_item[_edit]', 'li' );

		endif;

		// Link.
		echo beans_open_markup( 'beans_comment_item[_link]', 'li' );

			echo beans_open_markup( 'beans_comment_item_link[_link]', 'a', array(
				'href' => esc_url( get_comment_link( $comment->comment_ID ) )
			) );

				echo beans_output( 'beans_comment_link_text', __( 'Link', 'tm-beans' ) );

			echo beans_close_markup( 'beans_comment_item_link[_link]', 'a' );

		echo beans_close_markup( 'beans_comment_item[_link]', 'li' );

	echo beans_close_markup( 'beans_comment_links', 'ul' );

}


beans_add_smart_action( 'beans_no_comment', 'beans_no_comment' );

/**
 * Echo no comment content.
 *
 * @since 1.0.0
 */
function beans_no_comment() {

	echo beans_open_markup( 'beans_no_comment', 'p', 'class=uk-text-muted' );

		echo beans_output( 'beans_no_comment_text', __( 'No comment yet, add your voice below!', 'tm-beans' ) );

	echo beans_close_markup( 'beans_no_comment', 'p' );

}


beans_add_smart_action( 'beans_comments_closed', 'beans_comments_closed' );

/**
 * Echo closed comments content.
 *
 * @since 1.0.0
 */
function beans_comments_closed() {

	echo beans_open_markup( 'beans_comments_closed', 'p', array( 'class' => 'uk-alert uk-alert-warning uk-margin-bottom-remove' ) );

		echo beans_output( 'beans_comments_closed_text', __( 'Comments are closed for this article!', 'tm-beans' ) );

	echo beans_close_markup( 'beans_comments_closed', 'p' );

}


beans_add_smart_action( 'beans_comments_list_after_markup', 'beans_comments_navigation' );

/**
 * Echo comments navigation.
 *
 * @since 1.0.0
 */
function beans_comments_navigation() {

	if ( get_comment_pages_count() <= 1 && !get_option( 'page_comments' ) )
		return;

	echo beans_open_markup( 'beans_comments_navigation', 'ul', array(
		'class' => 'uk-pagination',
		'role' => 'navigation'
	) );

		// Previous.
		if ( get_previous_comments_link() ) {

			echo beans_open_markup( 'beans_comments_navigation_item[_previous]', 'li', array( 'class' => 'uk-pagination-previous' ) );

				$previous_icon = beans_open_markup( 'beans_previous_icon[_comments_navigation]', 'i', array(
					'class' => 'uk-icon-angle-double-left uk-margin-small-right'
				) );
				$previous_icon .= beans_close_markup( 'beans_previous_icon[_comments_navigation]', 'i' );

				echo get_previous_comments_link(
					$previous_icon . beans_output( 'beans_previous_text[_comments_navigation]', __( 'Previous', 'tm-beans' ) )
				);

			echo beans_close_markup( 'beans_comments_navigation_item[_previous]', 'li' );

		}

		// Next.
		if ( get_next_comments_link() ) {

			echo beans_open_markup( 'beans_comments_navigation_item[_next]', 'li', array( 'class' => 'uk-pagination-next' ) );

				$next_icon = beans_open_markup( 'beans_next_icon[_comments_navigation]', 'i', array(
					'class' => 'uk-icon-angle-double-right uk-margin-small-right'
				) );
				$next_icon .= beans_close_markup( 'beans_previous_icon[_comments_navigation]', 'i' );

				echo get_next_comments_link(
					beans_output( 'beans_next_text[_comments_navigation]', __( 'Next', 'tm-beans' ) ) . $next_icon
				);

			echo beans_close_markup( 'beans_comments_navigation_item_[_next]', 'li' );

		}

	echo beans_close_markup( 'beans_comments_navigation', 'ul' );

}





beans_add_smart_action( 'beans_after_open_comments', 'beans_comment_form_divider' );

/**
 * Echo comment divider.
 *
 * @since 1.0.0
 */
function beans_comment_form_divider() {

	echo beans_selfclose_markup( 'beans_comment_form_divider', 'hr', array( 'class' => 'uk-article-divider' ) );

}



beans_add_smart_action( 'beans_after_open_comments', 'beans_comment_form' );

/**
 * Echo comment navigation.
 *
 * @since 1.0.0
 */
function beans_comment_form() {

	$output = beans_open_markup( 'beans_comment_form_wrap', 'div', array( 'class' => 'uk-form tm-comment-form-wrap' ) );

		$output .= beans_render_function( 'comment_form', array(
			'title_reply' => beans_output( 'beans_comment_form_title_text', __( 'Add a Comment', 'tm-beans' ) )
		) );

	$output .= beans_close_markup( 'beans_comment_form_wrap', 'div' );

	$submit = beans_open_markup( 'beans_comment_form_submit', 'button', array( 'class' => 'uk-button uk-button-primary', 'type' => 'submit') );

		$submit .= beans_output( 'beans_comment_form_submit_text', __( 'Post Comment', 'tm-beans' ) );

	$submit .= beans_close_markup( 'beans_comment_form_submit', 'button' );

	// WordPress, please make it easier for us.
	echo preg_replace( '#<input[^>]+type="submit"[^>]+>#', $submit, $output );

}


// Filter.
beans_add_smart_action( 'cancel_comment_reply_link', 'beans_comment_cancel_reply_link', 10 , 3 );

/**
 * Echo comment cancel reply link.
 *
 * This function replaces the default WordPress comment cancel reply link.
 *
 * @since 1.0.0
 */
function beans_comment_cancel_reply_link( $html, $link, $text ) {

	echo beans_open_markup( 'beans_comment_cancel_reply_link', 'a', array(
		'rel' => 'nofollow',
		'id' => 'cancel-comment-reply-link',
		'class' => 'uk-button uk-button-small uk-button-danger uk-margin-small-right',
		'style' => isset( $_GET['replytocom'] ) ? '' : 'display:none;',
		'href' => $link
	) );

		echo beans_output( 'beans_comment_cancel_reply_link_text', $text );

	echo beans_close_markup( 'beans_comment_cancel_reply_link', 'a' );

}


// Filter.
beans_add_smart_action( 'comment_form_default_fields', 'beans_comment_form_fields' );

/**
 * Modify comment form fields.
 *
 * This function replaces the default WordPress comment fields.
 *
 * @since 1.0.0
 *
 * @param array $fields The WordPress default fields.
 *
 * @return array The modified fields.
 */
function beans_comment_form_fields( $fields ) {

	$commenter = wp_get_current_commenter();

	// Author.
	$author = beans_open_markup( 'beans_comment_form[_name]', 'div', array( 'class' => 'uk-width-medium-1-3 uk-margin-bottom' ) );

		/**
		 * Filter whether the comment form name legend should load or not.
		 *
		 * @since 1.0.0
		 */
		if ( beans_apply_filters( 'beans_comment_form_legend[_name]', true ) ) {

			$author .= beans_open_markup( 'beans_comment_form_legend[_name]', 'legend' );

				$author .= beans_output( 'beans_comment_form_legend_text[_name]', __( 'Name', 'tm-beans' ) );

			$author .= beans_close_markup( 'beans_comment_form_legend[_name]', 'legend' );

		}

		$author .= beans_selfclose_markup( 'beans_comment_form_field[_name]', 'input', array(
			'id' => 'author',
			'class' => 'uk-width-1-1',
			'type' => 'text',
			'value' => esc_attr( $commenter['comment_author'] ),
			'name' => 'author'
		) );

	$author .= beans_close_markup( 'beans_comment_form[_name]', 'div' );

	// Email.
	$email = beans_open_markup( 'beans_comment_form[_email]', 'div', array( 'class' => 'uk-width-medium-1-3 uk-margin-bottom' ) );

		/**
		 * Filter whether the comment form email legend should load or not.
		 *
		 * @since 1.0.0
		 */
		if ( beans_apply_filters( 'beans_comment_form_legend[_email]', true ) ) {

			$email .= beans_open_markup( 'beans_comment_form_legend[_email]', 'legend' );

				$email .= beans_output( 'beans_comment_form_legend_text[_email]', sprintf( __( 'Email %s', 'tm-beans' ), ( get_option( 'require_name_email' ) ? ' *' : '' ) ) );

			$email .= beans_close_markup( 'beans_comment_form_legend[_email]', 'legend' );

		}

		$email .= beans_selfclose_markup( 'beans_comment_form_field[_email]', 'input', array(
			'id' => 'email',
			'class' => 'uk-width-1-1',
			'type' => 'text',
			'value' => esc_attr(  $commenter['comment_author_email'] ),
			'name' => 'email',
			'required' => get_option( 'require_name_email' ) ? '' : null
		) );

	$email .= beans_close_markup( 'beans_comment_form[_email]', 'div' );

	// Url.
	$url = beans_open_markup( 'beans_comment_form[_website]', 'div', array( 'class' => 'uk-width-medium-1-3 uk-margin-bottom' ) );

		/**
		 * Filter whether the comment form url legend should load or not.
		 *
		 * @since 1.0.0
		 */
		if ( beans_apply_filters( 'beans_comment_form_legend[_url]', true ) ) {

			$url .= beans_open_markup( 'beans_comment_form_legend', 'legend' );

				$url .= beans_output( 'beans_comment_form_legend_text[_url]', __( 'Website', 'tm-beans' ) );

			$url .= beans_close_markup( 'beans_comment_form_legend[_url]', 'legend' );

		}

		$url .= beans_selfclose_markup( 'beans_comment_form_field[_url]', 'input', array(
			'id' => 'url',
			'class' => 'uk-width-1-1',
			'type' => 'text',
			'value' => esc_attr(  $commenter['comment_author_url'] ),
			'name' => 'url'
		) );

	$url .= beans_close_markup( 'beans_comment_form[_website]', 'div' );

	$fields = array(
		'author' => $author,
		'email' => $email,
		'url' => $url
	);

	return $fields;

}


// Filter.
beans_add_smart_action( 'comment_form_field_comment', 'beans_comment_form_comment' );

/**
 * Echo comment textarea field.
 *
 * This function replaces the default WordPress comment textarea field.
 *
 * @since 1.0.0
 */
function beans_comment_form_comment() {

	echo beans_open_markup( 'beans_comment_form[_comment]', 'p', array( 'class' => 'uk-width-medium-1-1' ) );

		/**
		 * Filter whether the comment form textarea legend should load or not.
		 *
		 * @since 1.0.0
		 */
		if ( beans_apply_filters( 'beans_comment_form_legend[_comment]', true ) ) {

			echo beans_open_markup( 'beans_comment_form_legend[_comment]', 'legend' );

				echo beans_output( 'beans_comment_form_legend_text[_comment]', __( 'Comment *', 'tm-beans' ) );

			echo beans_close_markup( 'beans_comment_form_legend[_comment]', 'legend' );

		}

		echo beans_open_markup( 'beans_comment_form_field[_comment]', 'textarea', array(
			'id' => 'comment',
			'class' => 'uk-width-1-1',
			'name' => 'comment',
			'required' => '',
			'rows' => 8,
		) );

		echo beans_close_markup( 'beans_comment_form_field[_comment]', 'textarea' );

	echo beans_close_markup( 'beans_comment_form[_comment]', 'p' );

}


beans_add_smart_action( 'comment_form_before_fields', 'beans_comment_before_fields' );

/**
 * Echo comment fields opening wraps.
 *
 * This function must be attached to the WordPress 'comment_form_before_fields' action which is only called if
 * the user is not logged in.
 *
 * @since 1.0.0
 */
function beans_comment_before_fields() {

	echo beans_open_markup( 'beans_comment_all_fields_wrap', 'div', array( 'class' => 'uk-grid' ) );

		echo beans_open_markup( 'beans_comment_fields_wrap', 'div', array( 'class' => 'uk-width-medium-1-1' ) );

			echo beans_open_markup( 'beans_comment_fields_inner_wrap', 'div', array( 'class' => 'uk-grid' ) );

}


beans_add_smart_action( 'comment_form_after_fields', 'beans_comment_form_after_fields' );

/**
 * Echo comment fields closing wraps.
 *
 * This function must be attached to the WordPress 'comment_form_after_fields' action which is only called if
 * the user is not logged in.
 *
 * @since 1.0.0
 */
function beans_comment_form_after_fields() {

			echo beans_close_markup( 'beans_comment_fields_inner_wrap', 'div' );

		echo beans_close_markup( 'beans_comment_fields_wrap', 'div' );

	// Add the all field closing wrap after textarea.
	echo _beans_add_anonymous_action( 'comment_form_field_comment', array( 'beans_close_markup', array( 'beans_comment_all_fields_wrap', 'div' ) ) );

}