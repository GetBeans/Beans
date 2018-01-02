<?php
/**
 * Echo post fragments.
 *
 * @package Fragments\Post
 */

beans_add_smart_action( 'beans_post_header', 'beans_post_title' );
/**
 * Echo post title.
 *
 * @since 1.0.0
 */
function beans_post_title() {

	$title = beans_output( 'beans_post_title_text', get_the_title() );
	$title_tag = 'h1';

	if ( empty( $title ) ) {
		return;
	}

	if ( ! is_singular() ) {

		$title_link = beans_open_markup( 'beans_post_title_link', 'a', array(
			'href'  => get_permalink(), // Automatically escaped.
			'title' => the_title_attribute( 'echo=0' ),
			'rel'   => 'bookmark',
		) );

			$title_link .= $title;

		$title_link .= beans_close_markup( 'beans_post_title_link', 'a' );

		$title = $title_link;
		$title_tag = 'h2';

	}

	beans_open_markup_e( 'beans_post_title', $title_tag, array(
		'class'    => 'uk-article-title',
		'itemprop' => 'headline',
	) );

		echo $title;

	beans_close_markup_e( 'beans_post_title', $title_tag );

}

beans_add_smart_action( 'beans_before_loop', 'beans_post_search_title' );
/**
 * Echo search post title.
 *
 * @since 1.0.0
 */
function beans_post_search_title() {

	if ( ! is_search() ) {
		return;
	}

	beans_open_markup_e( 'beans_search_title', 'h1', array( 'class' => 'uk-article-title' ) );

		printf( '%1$s%2$s', beans_output( 'beans_search_title_text', __( 'Search results for: ', 'tm-beans' ) ), get_search_query() );

	beans_close_markup_e( 'beans_search_title', 'h1' );

}


beans_add_smart_action( 'beans_before_loop', 'beans_post_archive_title' );
/**
 * Echo archive post title.
 *
 * @since 1.4.0
 */
function beans_post_archive_title() {

	if ( ! is_archive() ) {
		return;
	}

	beans_open_markup_e( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );

		beans_output_e( 'beans_archive_title_text', get_the_archive_title() );

	beans_close_markup_e( 'beans_archive_title', 'h1' );

}

beans_add_smart_action( 'beans_post_header', 'beans_post_meta', 15 );
/**
 * Echo post meta.
 *
 * @since 1.0.0
 */
function beans_post_meta() {

	/**
	 * Filter whether {@see beans_post_meta()} should be short-circuit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_post_meta', 'post' != get_post_type() ) ) {
		return;
	}

	beans_open_markup_e( 'beans_post_meta', 'ul', array( 'class' => 'uk-article-meta uk-subnav uk-subnav-line' ) );

		/**
		 * Filter the post meta actions and order.
		 *
		 * A do_action( "beans_post_meta_{$array_key}" ) is called for each array key set. Array values are used to set the priority of
		 * each actions. The array ordered using asort();
		 *
		 * @since 1.0.0
		 *
		 * @param array $fragments An array of fragment files.
		 */
		$meta_items = apply_filters( 'beans_post_meta_items', array(
			'date'     => 10,
			'author'   => 20,
			'comments' => 30,
		) );

		asort( $meta_items );

		foreach ( $meta_items as $meta => $priority ) {

			if ( ! $content = beans_render_function( 'do_action', "beans_post_meta_$meta" ) ) {
				continue;
			}

			beans_open_markup_e( "beans_post_meta_item[_{$meta}]", 'li' );

				beans_output_e( "beans_post_meta_item_{$meta}_text", $content );

			beans_close_markup_e( "beans_post_meta_item[_{$meta}]", 'li' );

		}

	beans_close_markup_e( 'beans_post_meta', 'ul' );

}

beans_add_smart_action( 'beans_post_body', 'beans_post_image', 5 );
/**
 * Echo post image.
 *
 * @since 1.0.0
 */
function beans_post_image() {

	if ( ! has_post_thumbnail() || ! current_theme_supports( 'post-thumbnails' ) ) {
		return false;
	}

	global $post;

	/**
	 * Filter whether Beans should handle the image edition (resize) or let WP do so.
	 *
	 * @since 1.2.5
	 *
	 * @param bool $edit True to use Beans Image API to handle the image edition (resize), false to let {@link http://codex.wordpress.org/Function_Reference/the_post_thumbnail the_post_thumbnail()} taking care of it. Default true.
	 */
	$edit = apply_filters( 'beans_post_image_edit', true );

	if ( $edit ) {

		/**
		 * Filter the arguments used by {@see beans_edit_image()} to edit the post image.
		 *
		 * @since 1.0.0
		 *
		 * @param bool|array $edit_args Arguments used by {@see beans_edit_image()}. Set to false to use WordPress
		 *                              large size.
		 */
		$edit_args = apply_filters( 'beans_edit_post_image_args', array(
			'resize' => array( 800, false ),
		) );

		if ( empty( $edit_args ) ) {
			$image = beans_get_post_attachment( $post->ID, 'large' );
		} else {
			$image = beans_edit_post_attachment( $post->ID, $edit_args );
		}

		/**
		 * Filter the arguments used by {@see beans_edit_image()} to edit the post small image.
		 *
		 * The small image is only used for screens equal or smaller than the image width set, which is 480px by default.
		 *
		 * @since 1.0.0
		 *
		 * @param bool|array $edit_args Arguments used by {@see beans_edit_image()}. Set to false to use WordPress
		 *                              small size.
		 */
		$edit_small_args = apply_filters( 'beans_edit_post_image_small_args', array(
			'resize' => array( 480, false ),
		) );

		if ( empty( $edit_small_args ) ) {
			$image_small = beans_get_post_attachment( $post->ID, 'thumbnail' );
		} else {
			$image_small = beans_edit_post_attachment( $post->ID, $edit_small_args );
		}
	}

	beans_open_markup_e( 'beans_post_image', 'div', array( 'class' => 'tm-article-image' ) );

		if ( ! is_singular() ) {
			beans_open_markup_e( 'beans_post_image_link', 'a', array(
				'href'  => get_permalink(), // Automatically escaped.
				'title' => the_title_attribute( 'echo=0' ),
			) );
		}

			beans_open_markup_e( 'beans_post_image_item_wrap', 'picture' );

				if ( $edit ) {

					beans_selfclose_markup_e( 'beans_post_image_small_item', 'source', array(
						'media'  => '(max-width: ' . $image_small->width . 'px)',
						'srcset' => esc_url( $image_small->src ),
					), $image_small );

					beans_selfclose_markup_e( 'beans_post_image_item', 'img', array(
						'width'    => $image->width,
						'height'   => $image->height,
						'src'      => $image->src, // Automatically escaped.
						'alt'      => $image->alt, // Automatically escaped.
						'itemprop' => 'image',
					), $image );

				} else {

					// Beans API isn't available, use wp_get_attachment_image_attributes filter instead.
					the_post_thumbnail();

				}

			beans_close_markup_e( 'beans_post_image_item_wrap', 'picture' );

		if ( ! is_singular() ) {
			beans_close_markup_e( 'beans_post_image_link', 'a' );
		}

	beans_close_markup_e( 'beans_post_image', 'div' );

}

beans_add_smart_action( 'beans_post_body', 'beans_post_content' );
/**
 * Echo post content.
 *
 * @since 1.0.0
 */
function beans_post_content() {

	global $post;

	beans_open_markup_e( 'beans_post_content', 'div', array(
		'class'    => 'tm-article-content',
		'itemprop' => 'text',
	) );

		the_content();

		if ( is_singular() && 'open' === get_option( 'default_ping_status' ) && post_type_supports( $post->post_type, 'trackbacks' ) ) {

			echo '<!--';
			trackback_rdf();
			echo '-->' . "\n";

		}

	beans_close_markup_e( 'beans_post_content', 'div' );

}

// Filter.
beans_add_smart_action( 'the_content_more_link', 'beans_post_more_link' );
/**
 * Modify post "more link".
 *
 * @since 1.0.0
 *
 * @return string The modified "more link".
 */
function beans_post_more_link() {

	global $post;

	$output = beans_open_markup( 'beans_post_more_link', 'a', array(
		'href'  => get_permalink(), // Automatically escaped.
		'class' => 'more-link',
	) );

		$output .= beans_output( 'beans_post_more_link_text', __( 'Continue reading', 'tm-beans' ) );

		$output .= beans_open_markup( 'beans_next_icon[_more_link]', 'i', array(
			'class' => 'uk-icon-angle-double-right uk-margin-small-left',
		) );
		$output .= beans_close_markup( 'beans_next_icon[_more_link]', 'i' );

	$output .= beans_close_markup( 'beans_post_more_link', 'a' );

	return $output;

}

beans_add_smart_action( 'beans_post_body', 'beans_post_content_navigation', 20 );
/**
 * Echo post content navigation.
 *
 * @since 1.0.0
 */
function beans_post_content_navigation() {

	echo wp_link_pages( array(
		'before' => beans_open_markup( 'beans_post_content_navigation', 'p', array( 'class' => 'uk-text-bold' ) ) . beans_output( 'beans_post_content_navigation_text', __( 'Pages:', 'tm-beans' ) ),
		'after'  => beans_close_markup( 'beans_post_content_navigation', 'p' ),
		'echo'   => false,
	) );

}

beans_add_smart_action( 'beans_post_body', 'beans_post_meta_categories', 25 );
/**
 * Echo post meta categories.
 *
 * @since 1.0.0
 */
function beans_post_meta_categories() {

	if ( ! $categories = beans_render_function( 'do_shortcode', '[beans_post_meta_categories]' ) ) {
		return;
	}

	beans_open_markup_e( 'beans_post_meta_categories', 'span', array( 'class' => 'uk-text-small uk-text-muted uk-clearfix' ) );

		echo $categories;

	beans_close_markup_e( 'beans_post_meta_categories', 'span' );

}

beans_add_smart_action( 'beans_post_body', 'beans_post_meta_tags', 30 );
/**
 * Echo post meta tags.
 *
 * @since 1.0.0
 */
function beans_post_meta_tags() {

	if ( ! $tags = beans_render_function( 'do_shortcode', '[beans_post_meta_tags]' ) ) {
		return;
	}

	beans_open_markup_e( 'beans_post_meta_tags', 'span', array( 'class' => 'uk-text-small uk-text-muted uk-clearfix' ) );

		echo $tags;

	beans_close_markup_e( 'beans_post_meta_tags', 'span' );

}

// Filter.
beans_add_smart_action( 'previous_post_link', 'beans_previous_post_link', 10, 4 );
/**
 * Modify post "previous link".
 *
 * @since 1.0.0
 *
 * @return string The modified "previous link".
 */
function beans_previous_post_link( $output, $format, $link, $post ) {

	// Using $link won't apply wp filters, so rather strip tags the $output.
	$text = strip_tags( $output );

	$output = beans_open_markup( 'beans_previous_link[_post_navigation]', 'a', array(
		'href'  => get_permalink( $post ), // Automatically escaped.
		'rel'   => 'previous',
		'title' => $post->post_title, // Automatically escaped.
	) );

		$output .= beans_open_markup( 'beans_previous_icon[_post_navigation]', 'i', array(
			'class' => 'uk-icon-angle-double-left uk-margin-small-right',
		) );

		$output .= beans_close_markup( 'beans_previous_icon[_post_navigation]', 'i' );

		$output .= beans_output( 'beans_previous_text[_post_navigation]', $text );

	$output .= beans_close_markup( 'beans_previous_link[_post_navigation]', 'a' );

	return $output;

}

// Filter.
beans_add_smart_action( 'next_post_link', 'beans_next_post_link', 10, 4 );
/**
 * Modify post "next link".
 *
 * @since 1.0.0
 *
 * @return string The modified "next link".
 */
function beans_next_post_link( $output, $format, $link, $post ) {

	// Using $link won't apply wp filters, so rather strip tags the $output.
	$text = strip_tags( $output );

	$output = beans_open_markup( 'beans_next_link[_post_navigation]', 'a', array(
		'href'  => get_permalink( $post ), // Automatically escaped.
		'rel'   => 'next',
		'title' => $post->post_title, // Automatically escaped.
	) );

		$output .= beans_output( 'beans_next_text[_post_navigation]', $text );

		$output .= beans_open_markup( 'beans_next_icon[_post_navigation]', 'i', array(
			'class' => 'uk-icon-angle-double-right uk-margin-small-left',
		) );

		$output .= beans_close_markup( 'beans_next_icon[_post_navigation]', 'i' );

	$output .= beans_close_markup( 'beans_next_link[_post_navigation]', 'a' );

	return $output;

}

beans_add_smart_action( 'beans_post_after_markup', 'beans_post_navigation' );
/**
 * Echo post navigation.
 *
 * @since 1.0.0
 */
function beans_post_navigation() {

	/**
	 * Filter whether {@see beans_post_navigation()} should be short-circuit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_post_navigation', ! is_singular( 'post' ) ) ) {
		return;
	}

	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	beans_open_markup_e( 'beans_post_navigation', 'ul', array(
		'class' => 'uk-pagination',
		'role'  => 'navigation',
	) );

		if ( $previous ) {

			// Previous.
			beans_open_markup_e( 'beans_post_navigation_item[_previous]', 'li', array( 'class' => 'uk-pagination-previous' ) );

				echo get_previous_post_link( '%link', __( 'Previous', 'tm-beans' ) );

			beans_close_markup_e( 'beans_post_navigation_item[_previous]', 'li' );

		}

		if ( $next ) {

			// Next.
			beans_open_markup_e( 'beans_post_navigation_item[_next]', 'li', array( 'class' => 'uk-pagination-next' ) );

				echo get_next_post_link( '%link', __( 'Next', 'tm-beans' ) );

			beans_close_markup_e( 'beans_post_navigation_item[_next]', 'li' );

		}

	beans_close_markup_e( 'beans_post_navigation', 'ul' );

}

beans_add_smart_action( 'beans_after_posts_loop', 'beans_posts_pagination' );
/**
 * Echo posts pagination.
 *
 * @since 1.0.0
 */
function beans_posts_pagination() {

	/**
	 * Filter whether {@see beans_posts_pagination()} should be short-circuit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_post_pagination', is_singular() ) ) {
		return;
	}

	global $wp_query;

	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$current = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$count = intval( $wp_query->max_num_pages );

	beans_open_markup_e( 'beans_posts_pagination', 'ul', array(
		'class' => 'uk-pagination uk-grid-margin',
		'role'  => 'navigation',
	) );

		// Previous.
		if ( get_previous_posts_link() ) {

			beans_open_markup_e( 'beans_posts_pagination_item[_previous]', 'li' );

				beans_open_markup_e( 'beans_previous_link[_posts_pagination]', 'a', array(
					'href' => previous_posts( false ), // Automatically escaped.
				), $current );

					beans_open_markup_e( 'beans_previous_icon[_posts_pagination]', 'i', array(
						'class' => 'uk-icon-angle-double-left uk-margin-small-right',
					) );

					beans_close_markup_e( 'beans_previous_icon[_posts_pagination]', 'i' );

					beans_output_e( 'beans_previous_text[_posts_pagination]', __( 'Previous', 'tm-beans' ) );

				beans_close_markup_e( 'beans_previous_link[_posts_pagination]', 'a' );

			beans_close_markup_e( 'beans_posts_pagination_item[_previous]', 'li' );

		}

		// Links.
		foreach ( range( 1, $wp_query->max_num_pages ) as $link ) {

			// Skip if next is set.
			if ( isset( $next ) && $link != $next ) {
				continue;
			} else {
				$next = $link + 1;
			}

			$is_separator = array(
				1 != $link, // Not first.
				1 == $current && 3 == $link ? false : true, // Force first 3 items.
				$count > 3, // More.
				$count != $link, // Not last.
				( $current - 1 ) != $link, // Not previous.
				$current != $link, // Not current.
				( $current + 1 ) != $link, // Not next.
			);

			// Separator.
			if ( ! in_array( false, $is_separator ) ) {

				beans_open_markup_e( 'beans_posts_pagination_item[_separator]', 'li' );

					beans_output_e( 'beans_posts_pagination_item_separator_text', '...' );

				beans_close_markup_e( 'beans_posts_pagination_item[_separator]', 'li' );

				// Jump.
				if ( $link < $current ) {
					$next = $current - 1;
				} elseif ( $link > $current ) {
					$next = $count;
				}

				continue;

			}

			// Integer.
			if ( $link == $current ) {

				beans_open_markup_e( 'beans_posts_pagination_item[_active]', 'li', array( 'class' => 'uk-active' ) );

					beans_open_markup_e( 'beans_posts_pagination_item[_active]_wrap', 'span' );

						beans_output_e( 'beans_posts_pagination_item[_active]_text', $link );

					beans_close_markup_e( 'beans_posts_pagination_item[_active]_wrap', 'span' );

				beans_close_markup_e( 'beans_posts_pagination_item[_active]', 'li' );

			} else {

				beans_open_markup_e( 'beans_posts_pagination_item', 'li' );

					beans_open_markup_e( 'beans_posts_pagination_item_link', 'a', array(
						'href' => get_pagenum_link( $link ), // Automatically escaped.
					), $link );

						beans_output_e( 'beans_posts_pagination_item_link_text', $link );

					beans_close_markup_e( 'beans_posts_pagination_item_link', 'a' );

				beans_close_markup_e( 'beans_posts_pagination_item', 'li' );

			}
		}

		// Next.
		if ( get_next_posts_link() ) {

			beans_open_markup_e( 'beans_posts_pagination_item[_next]', 'li' );

				beans_open_markup_e( 'beans_next_link[_posts_pagination]', 'a', array(
					'href' => next_posts( $count, false ), // Automatically escaped.
				), $current );

					beans_output_e( 'beans_next_text[_posts_pagination]', __( 'Next', 'tm-beans' ) );

					beans_open_markup_e( 'beans_next_icon[_posts_pagination]', 'i', array(
						'class' => 'uk-icon-angle-double-right uk-margin-small-left',
					) );

					beans_close_markup_e( 'beans_next_icon[_posts_pagination]', 'i' );

				beans_close_markup_e( 'beans_next_link[_posts_pagination]', 'a' );

			beans_close_markup_e( 'beans_posts_pagination_item[_next]', 'li' );

		}

	beans_close_markup_e( 'beans_posts_pagination', 'ul' );

}

beans_add_smart_action( 'beans_no_post', 'beans_no_post' );
/**
 * Echo no post content.
 *
 * @since 1.0.0
 */
function beans_no_post() {

	beans_open_markup_e( 'beans_post', 'article', array( 'class' => 'tm-no-article uk-article' . ( current_theme_supports( 'beans-default-styling' ) ? ' uk-panel-box' : null ) ) );

		beans_open_markup_e( 'beans_post_header', 'header' );

			beans_open_markup_e( 'beans_post_title', 'h1', array( 'class' => 'uk-article-title' ) );

				beans_output_e( 'beans_no_post_article_title_text', __( 'Whoops, no result found!', 'tm-beans' ) );

			beans_close_markup_e( 'beans_post_title', 'h1' );

		beans_close_markup_e( 'beans_post_header', 'header' );

		beans_open_markup_e( 'beans_post_body', 'div' );

			beans_open_markup_e( 'beans_post_content', 'div', array( 'class' => 'tm-article-content' ) );

				beans_open_markup_e( 'beans_no_post_article_content', 'p', array( 'class' => 'uk-alert uk-alert-warning' ) );

					beans_output_e( 'beans_no_post_article_content_text', __( 'It looks like nothing was found at this location. Maybe try a search?', 'tm-beans' ) );

				beans_close_markup_e( 'beans_no_post_article_content', 'p' );

					beans_output_e( 'beans_no_post_search_form', get_search_form( false ) );

			beans_close_markup_e( 'beans_post_content', 'div' );

		beans_close_markup_e( 'beans_post_body', 'div' );

	beans_close_markup_e( 'beans_post', 'article' );

}

// Filter.
beans_add_smart_action( 'the_password_form', 'beans_post_password_form' );
/**
 * Modify password protected form.
 *
 * @since 1.0.0
 *
 * @return string The form.
 */
function beans_post_password_form() {

	global $post;

	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );

	// Notice.
	$output = beans_open_markup( 'beans_password_form_notice', 'p', array( 'class' => 'uk-alert uk-alert-warning' ) );

		$output .= beans_output( 'beans_password_form_notice_text', __( 'This post is protected. To view it, enter the password below!', 'tm-beans' ) );

	$output .= beans_close_markup( 'beans_password_form_notice', 'p' );

	// Form.
	$output .= beans_open_markup( 'beans_password_form', 'form', array(
		'class'  => 'uk-form uk-margin-bottom',
		'method' => 'post',
		'action' => site_url( 'wp-login.php?action=postpass', 'login_post' ), // Automatically escaped.
	) );

		$output .= beans_selfclose_markup( 'beans_password_form_input', 'input', array(
			'class'       => 'uk-margin-small-top uk-margin-small-right',
			'type'        => 'password',
			'placeholder' => apply_filters( 'beans_password_form_input_placeholder', __( 'Password', 'tm-beans' ) ), // Automatically escaped.
			'name'        => 'post_password',
		) );

		$output .= beans_selfclose_markup( 'beans_password_form_submit', 'input', array(
			'class' => 'uk-button uk-margin-small-top',
			'type'  => 'submit',
			'name'  => 'submit',
			'value' => esc_attr( apply_filters( 'beans_password_form_submit_text', __( 'Submit', 'tm-beans' ) ) ),
		) );

	$output .= beans_close_markup( 'beans_password_form', 'form' );

	return $output;

}

// Filter.
beans_add_smart_action( 'post_gallery', 'beans_post_gallery', 10, 3 );
/**
 * Modify WP {@link https://codex.wordpress.org/Function_Reference/gallery_shortcode Gallery Shortcode} output.
 *
 * This implements the functionality of the Gallery Shortcode for displaying WordPress images in a post.
 *
 * @since 1.3.0
 *
 * @param string $output   The gallery output. Default empty.
 * @param array  $attr     Attributes of the {@link https://codex.wordpress.org/Function_Reference/gallery_shortcode gallery_shortcode()}.
 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
 *
 * @return string HTML content to display gallery.
 */
function beans_post_gallery( $output, $attr, $instance ) {

	$post = get_post();
	$html5 = current_theme_supports( 'html5', 'gallery' );
	$defaults = array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'figure' : 'dl',
		'icontag'    => $html5 ? 'div' : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => '',
	);
	$atts = shortcode_atts( $defaults, $attr, 'gallery' );
	$id = intval( $atts['id'] );

	// Set attachements.
	if ( ! empty( $atts['include'] ) ) {

		$_attachments = get_posts( array(
			'include'        => $atts['include'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );

		$attachments = array();

		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {

		$attachments = get_children( array(
			'post_parent'    => $id,
			'exclude'        => $atts['exclude'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );

	} else {

		$attachments = get_children( array(
			'post_parent'    => $id,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );

	}

	// Stop here if no attachment.
	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {

		$output = "\n";

		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
		}

		return $output;

	}

	// Valid tags.
	$valid_tags = wp_kses_allowed_html( 'post' );
	$validate = array(
		'itemtag',
		'captiontag',
		'icontag',
	);

	// Validate tags.
	foreach ( $validate as $tag ) {
		if ( ! isset( $valid_tags[ $atts[ $tag ] ] ) ) {
			$atts[ $tag ] = $defaults[ $tag ];
		}
	}

	// Set variables used in the output.
	$columns = intval( $atts['columns'] );
	$size_class = sanitize_html_class( $atts['size'] );

	// WP adds the opening div in the gallery_style filter (weird), so we follow it as don't want to break people's site.
	$gallery_div = beans_open_markup( "beans_post_gallery[_{$id}]", 'div', array(
		'class'               => "uk-grid uk-grid-width-small-1-{$columns} gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}", // Automatically escaped.
		'data-uk-grid-margin' => false,
	), $id, $columns );

	/**
	 * Apply WP core filter. Filter the default gallery shortcode CSS styles.
	 *
	 * Documented in WordPress.
	 *
	 * @ignore
	 */
	$output = apply_filters( 'gallery_style', $gallery_div );

		$i = 0; foreach ( $attachments as $attachment_id => $attachment ) {

			$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "gallery-{$instance}-{$id}" ) : '';
			$image_meta = wp_get_attachment_metadata( $attachment_id );
			$orientation = '';

			if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
			}

			// Set the image output.
			if ( 'none' === $atts['link'] ) {
				$image_output = wp_get_attachment_image( $attachment_id, $atts['size'], false, $attr );
			} else {
				$image_output = wp_get_attachment_link( $attachment_id, $atts['size'], ( 'file' !== $atts['link'] ), false, false, $attr );
			}

			$output .= beans_open_markup( "beans_post_gallery_item[_{$attachment_id}]", $atts['itemtag'], array( 'class' => 'gallery-item' ) );

				$output .= beans_open_markup( "beans_post_gallery_icon[_{$attachment_id}]", $atts['icontag'], array( 'class' => "gallery-icon {$orientation}" ) ); // Automatically escaped.

					$output .= beans_output( "beans_post_gallery_icon[_{$attachment_id}]", $image_output, $attachment_id, $atts );

				$output .= beans_close_markup( "beans_post_gallery_icon[_{$attachment_id}]", $atts['icontag'] );

				if ( $atts['captiontag'] && trim( $attachment->post_excerpt ) ) {

					$output .= beans_open_markup( "beans_post_gallery_caption[_{$attachment_id}]", $atts['captiontag'], array( 'class' => 'wp-caption-text gallery-caption' ) );

						$output .= beans_output( "beans_post_gallery_caption_text[_{$attachment_id}]", wptexturize( $attachment->post_excerpt ) );

					$output .= beans_close_markup( "beans_post_gallery_caption[_{$attachment_id}]", $atts['captiontag'] );

				}

			$output .= beans_close_markup( "beans_post_gallery_item[_{$attachment_id}]", $atts['itemtag'] );

		}

	$output .= beans_close_markup( "beans_post_gallery[_{$id}]", 'div' );

	return $output;

}
