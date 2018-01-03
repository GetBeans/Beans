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

	beans_open_markup_e( 'beans_comments_title', 'h2' );

		beans_output_e( 'beans_comments_title_text', sprintf(
			_n( '%s Comment', '%s Comments', get_comments_number(), 'tm-beans' ),
			number_format_i18n( get_comments_number() )
		) );

	beans_close_markup_e( 'beans_comments_title', 'h2' );

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
	if ( ! $avatar = get_avatar( $comment, $comment->args['avatar_size'] ) ) {
		return;
	}

	beans_open_markup_e( 'beans_comment_avatar', 'div', array( 'class' => 'uk-comment-avatar' ) );

		echo $avatar;

	beans_close_markup_e( 'beans_comment_avatar', 'div' );

}

beans_add_smart_action( 'beans_comment_header', 'beans_comment_author' );
/**
 * Echo the comment author title.
 *
 * @since 1.0.0
 */
function beans_comment_author() {

	beans_open_markup_e( 'beans_comment_title', 'div', array(
		'class'     => 'uk-comment-title',
		'itemprop'  => 'author',
		'itemscope' => 'itemscope',
		'itemtype'  => 'http://schema.org/Person',
	) );

		echo get_comment_author_link();

	beans_close_markup_e( 'beans_comment_title', 'div' );

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
	if ( 'trackback' == $comment->comment_type ) {

		beans_open_markup_e( 'beans_trackback_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left' ) );

			beans_output_e( 'beans_trackback_text', __( 'Trackback', 'tm-beans' ) );

		beans_close_markup_e( 'beans_trackback_badge', 'span' );

	}

	// Pindback badge.
	if ( 'pingback' == $comment->comment_type ) {

		beans_open_markup_e( 'beans_pingback_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left' ) );

			beans_output_e( 'beans_pingback_text', __( 'Pingback', 'tm-beans' ) );

		beans_close_markup_e( 'beans_pingback_badge', 'span' );

	}

	// Moderation badge.
	if ( '0' == $comment->comment_approved ) {

		beans_open_markup_e( 'beans_moderation_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left uk-badge-warning' ) );

			beans_output_e( 'beans_moderation_text', __( 'Awaiting Moderation', 'tm-beans' ) );

		beans_close_markup_e( 'beans_moderation_badge', 'span' );

	}

	// Moderator badge.
	if ( user_can( $comment->user_id, 'moderate_comments' ) ) {

		beans_open_markup_e( 'beans_moderator_badge', 'span', array( 'class' => 'uk-badge uk-margin-small-left' ) );

			beans_output_e( 'beans_moderator_text', __( 'Moderator', 'tm-beans' ) );

		beans_close_markup_e( 'beans_moderator_badge', 'span' );

	}

}

beans_add_smart_action( 'beans_comment_header', 'beans_comment_metadata', 15 );
/**
 * Echo the comment metadata.
 *
 * @since 1.0.0
 */
function beans_comment_metadata() {

	beans_open_markup_e( 'beans_comment_meta', 'div', array( 'class' => 'uk-comment-meta' ) );

		beans_open_markup_e( 'beans_comment_time', 'time', array(
			'datetime' => get_comment_time( 'c' ),
			'itemprop' => 'datePublished',
		) );

			beans_output_e( 'beans_comment_time_text', sprintf(
				_x( '%1$s at %2$s', '1: date, 2: time', 'tm-beans' ),
				get_comment_date(),
				get_comment_time()
			) );

		beans_close_markup_e( 'beans_comment_time', 'time' );

	beans_close_markup_e( 'beans_comment_meta', 'div' );

}

beans_add_smart_action( 'beans_comment_content', 'beans_comment_content' );
/**
 * Echo the comment content.
 *
 * @since 1.0.0
 */
function beans_comment_content() {

	beans_output_e( 'beans_comment_content', beans_render_function( 'comment_text' ) );

}

beans_add_smart_action( 'beans_comment_content', 'beans_comment_links', 15 );
/**
 * Echo the comment links.
 *
 * @since 1.0.0
 */
function beans_comment_links() {

	global $comment;

	beans_open_markup_e( 'beans_comment_links', 'ul', array( 'class' => 'tm-comment-links uk-subnav uk-subnav-line' ) );

		// Reply.
		echo get_comment_reply_link( array_merge( $comment->args, array(
			'add_below' => 'comment-content',
			'depth'     => $comment->depth,
			'max_depth' => $comment->args['max_depth'],
			'before'    => beans_open_markup( 'beans_comment_item[_reply]', 'li' ),
			'after'     => beans_close_markup( 'beans_comment_item[_reply]', 'li' ),
		) ) );

		// Edit.
		if ( current_user_can( 'moderate_comments' ) ) :

			beans_open_markup_e( 'beans_comment_item[_edit]', 'li' );

				beans_open_markup_e( 'beans_comment_item_link[_edit]', 'a', array(
					'href' => get_edit_comment_link( $comment->comment_ID ), // Automatically escaped.
				) );

					beans_output_e( 'beans_comment_edit_text', __( 'Edit', 'tm-beans' ) );

				beans_close_markup_e( 'beans_comment_item_link[_edit]', 'a' );

			beans_close_markup_e( 'beans_comment_item[_edit]', 'li' );

		endif;

		// Link.
		beans_open_markup_e( 'beans_comment_item[_link]', 'li' );

			beans_open_markup_e( 'beans_comment_item_link[_link]', 'a', array(
				'href' => get_comment_link( $comment->comment_ID ), // Automatically escaped.
			) );

				beans_output_e( 'beans_comment_link_text', __( 'Link', 'tm-beans' ) );

			beans_close_markup_e( 'beans_comment_item_link[_link]', 'a' );

		beans_close_markup_e( 'beans_comment_item[_link]', 'li' );

	beans_close_markup_e( 'beans_comment_links', 'ul' );

}

beans_add_smart_action( 'beans_no_comment', 'beans_no_comment' );
/**
 * Echo no comment content.
 *
 * @since 1.0.0
 */
function beans_no_comment() {

	beans_open_markup_e( 'beans_no_comment', 'p', 'class=uk-text-muted' );

		beans_output_e( 'beans_no_comment_text', __( 'No comment yet, add your voice below!', 'tm-beans' ) );

	beans_close_markup_e( 'beans_no_comment', 'p' );

}

beans_add_smart_action( 'beans_comments_closed', 'beans_comments_closed' );
/**
 * Echo closed comments content.
 *
 * @since 1.0.0
 */
function beans_comments_closed() {

	beans_open_markup_e( 'beans_comments_closed', 'p', array( 'class' => 'uk-alert uk-alert-warning uk-margin-bottom-remove' ) );

		beans_output_e( 'beans_comments_closed_text', __( 'Comments are closed for this article!', 'tm-beans' ) );

	beans_close_markup_e( 'beans_comments_closed', 'p' );

}

beans_add_smart_action( 'beans_comments_list_after_markup', 'beans_comments_navigation' );
/**
 * Echo comments navigation.
 *
 * @since 1.0.0
 */
function beans_comments_navigation() {

	if ( get_comment_pages_count() <= 1 && ! get_option( 'page_comments' ) ) {
		return;
	}

	beans_open_markup_e( 'beans_comments_navigation', 'ul', array(
		'class' => 'uk-pagination',
		'role'  => 'navigation',
	) );

		// Previous.
		if ( get_previous_comments_link() ) {

			beans_open_markup_e( 'beans_comments_navigation_item[_previous]', 'li', array( 'class' => 'uk-pagination-previous' ) );

				$previous_icon = beans_open_markup( 'beans_previous_icon[_comments_navigation]', 'i', array(
					'class' => 'uk-icon-angle-double-left uk-margin-small-right',
				) );
				$previous_icon .= beans_close_markup( 'beans_previous_icon[_comments_navigation]', 'i' );

				echo get_previous_comments_link(
					$previous_icon . beans_output( 'beans_previous_text[_comments_navigation]', __( 'Previous', 'tm-beans' ) )
				);

			beans_close_markup_e( 'beans_comments_navigation_item[_previous]', 'li' );

		}

		// Next.
		if ( get_next_comments_link() ) {

			beans_open_markup_e( 'beans_comments_navigation_item[_next]', 'li', array( 'class' => 'uk-pagination-next' ) );

				$next_icon = beans_open_markup( 'beans_next_icon[_comments_navigation]', 'i', array(
					'class' => 'uk-icon-angle-double-right uk-margin-small-right',
				) );
				$next_icon .= beans_close_markup( 'beans_next_icon[_comments_navigation]', 'i' );

				echo get_next_comments_link(
					beans_output( 'beans_next_text[_comments_navigation]', __( 'Next', 'tm-beans' ) ) . $next_icon
				);

			beans_close_markup_e( 'beans_comments_navigation_item_[_next]', 'li' );

		}

	beans_close_markup_e( 'beans_comments_navigation', 'ul' );

}

beans_add_smart_action( 'beans_after_open_comments', 'beans_comment_form_divider' );
/**
 * Echo comment divider.
 *
 * @since 1.0.0
 */
function beans_comment_form_divider() {

	beans_selfclose_markup_e( 'beans_comment_form_divider', 'hr', array( 'class' => 'uk-article-divider' ) );

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
			'title_reply' => beans_output( 'beans_comment_form_title_text', __( 'Add a Comment', 'tm-beans' ) ),
		) );

	$output .= beans_close_markup( 'beans_comment_form_wrap', 'div' );

	$submit = beans_open_markup( 'beans_comment_form_submit', 'button', array(
		'class' => 'uk-button uk-button-primary',
		'type' => 'submit',
	) );

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

	$output = beans_open_markup( 'beans_comment_cancel_reply_link', 'a', array(
		'rel'   => 'nofollow',
		'id'    => 'cancel-comment-reply-link',
		'class' => 'uk-button uk-button-small uk-button-danger uk-margin-small-right',
		'style' => isset( $_GET['replytocom'] ) ? '' : 'display:none;',
		'href'  => $link, // Automatically escaped.
	) );

		$output .= beans_output( 'beans_comment_cancel_reply_link_text', $text );

	$output .= beans_close_markup( 'beans_comment_cancel_reply_link', 'a' );

	return $output;

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

	$output = beans_open_markup( 'beans_comment_form[_comment]', 'p', array( 'class' => 'uk-margin-top' ) );

		/**
		 * Filter whether the comment form textarea legend should load or not.
		 *
		 * @since 1.0.0
		 */
		if ( beans_apply_filters( 'beans_comment_form_legend[_comment]', true ) ) {

			$output .= beans_open_markup( 'beans_comment_form_legend[_comment]', 'legend' );

				$output .= beans_output( 'beans_comment_form_legend_text[_comment]', __( 'Comment *', 'tm-beans' ) );

			$output .= beans_close_markup( 'beans_comment_form_legend[_comment]', 'legend' );

		}

		$output .= beans_open_markup( 'beans_comment_form_field[_comment]', 'textarea', array(
			'id'       => 'comment',
			'class'    => 'uk-width-1-1',
			'name'     => 'comment',
			'required' => '',
			'rows'     => 8,
		) );

		$output .= beans_close_markup( 'beans_comment_form_field[_comment]', 'textarea' );

	$output .= beans_close_markup( 'beans_comment_form[_comment]', 'p' );

	return $output;

}

beans_add_smart_action( 'comment_form_before_fields', 'beans_comment_before_fields', 9999 );
/**
 * Echo comment fields opening wraps.
 *
 * This function must be attached to the WordPress 'comment_form_before_fields' action which is only called if
 * the user is not logged in.
 *
 * @since 1.0.0
 */
function beans_comment_before_fields() {

	beans_open_markup_e( 'beans_comment_fields_wrap', 'div', array( 'class' => 'uk-width-medium-1-1' ) );

		beans_open_markup_e( 'beans_comment_fields_inner_wrap', 'div', array(
			'class' => 'uk-grid uk-grid-small',
			'data-uk-grid-margin' => '',
		) );

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
	$grid = count( (array) $fields );

	// Author.
	if ( isset( $fields['author'] ) ) {

		$author = beans_open_markup( 'beans_comment_form[_name]', 'div', array( 'class' => "uk-width-medium-1-$grid" ) );

			/**
			 * Filter whether the comment form name legend should load or not.
			 *
			 * @since 1.0.0
			 */
			if ( beans_apply_filters( 'beans_comment_form_legend[_name]', true ) ) {

				$author .= beans_open_markup( 'beans_comment_form_legend[_name]', 'legend' );

					$author .= beans_output( 'beans_comment_form_legend_text[_name]', __( 'Name *', 'tm-beans' ) );

				$author .= beans_close_markup( 'beans_comment_form_legend[_name]', 'legend' );

			}

			$author .= beans_selfclose_markup( 'beans_comment_form_field[_name]', 'input', array(
				'id'       => 'author',
				'class'    => 'uk-width-1-1',
				'type'     => 'text',
				'value'    => $commenter['comment_author'], // Automatically escaped.
				'name'     => 'author',
				'required' => 'required',
			) );

		$author .= beans_close_markup( 'beans_comment_form[_name]', 'div' );

		$fields['author'] = $author;

	}

	// Email.
	if ( isset( $fields['email'] ) ) {

		$email = beans_open_markup( 'beans_comment_form[_email]', 'div', array( 'class' => "uk-width-medium-1-$grid" ) );

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
				'id'       => 'email',
				'class'    => 'uk-width-1-1',
				'type'     => 'text',
				'value'    => $commenter['comment_author_email'], // Automatically escaped.
				'name'     => 'email',
				'required' => get_option( 'require_name_email' ) ? 'required' : null,
			) );

		$email .= beans_close_markup( 'beans_comment_form[_email]', 'div' );

		$fields['email'] = $email;

	}

	// Url.
	if ( isset( $fields['url'] ) ) {

		$url = beans_open_markup( 'beans_comment_form[_website]', 'div', array( 'class' => "uk-width-medium-1-$grid" ) );

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
				'id'    => 'url',
				'class' => 'uk-width-1-1',
				'type'  => 'text',
				'value' => $commenter['comment_author_url'], // Automatically escaped.
				'name'  => 'url',
			) );

		$url .= beans_close_markup( 'beans_comment_form[_website]', 'div' );

		$fields['url'] = $url;

	}

	return $fields;
}

beans_add_smart_action( 'comment_form_after_fields', 'beans_comment_form_after_fields', 3 );
/**
 * Echo comment fields closing wraps.
 *
 * This function must be attached to the WordPress 'comment_form_after_fields' action which is only called if
 * the user is not logged in.
 *
 * @since 1.0.0
 */
function beans_comment_form_after_fields() {

		beans_close_markup_e( 'beans_comment_fields_inner_wrap', 'div' );

	beans_close_markup_e( 'beans_comment_fields_wrap', 'div' );

}
