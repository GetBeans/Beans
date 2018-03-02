<?php
/**
 * The Beans Templates API allows to load Beans template files as well as loading the entire document.
 *
 * @package Beans\Framework\API\Actions
 *
 * @since   1.0.0
 */

/**
 * Load and render the entire document (web page).  This function is the root of the Beans' framework hierarchy.
 * Therefore, when calling it, Beans runs, building the web page's HTML markup and rendering it out to the
 * browser.
 *
 * Here are some guidelines for calling this function:
 *
 *      - Call it from a primary template file, e.g. single.php, page.php, home.php, archive.php, etc.
 *      - Do all modifications and customizations before calling this function.
 *      - Put this function on the last line of code in the template file.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_load_document() {
	/**
	 * Fires before the document is loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_before_load_document' );

	/**
	 * Fires when the document loads.
	 *
	 * This hook is the root of Beans's framework hierarchy. It all starts here!
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_load_document' );

	/**
	 * Fires after the document is loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_after_load_document' );
}

/**
 * Loads a secondary template file.
 *
 * This function loads Beans's default template file. It must be called from a secondary template file
 * (e.g. comments.php) and must be the last function to be called. All modifications must be done before calling
 * this function. This includes modifying markup, attributes, fragments, etc.
 *
 * The default template files contain the hook on which the fragments are attached to. Bypassing this function
 * will completely remove the default content.
 *
 * @since 1.0.0
 *
 * @param string $file The filename of the secondary template files. __FILE__ is usually to argument to pass.
 *
 * @return bool True on success, false on failure.
 */
function beans_load_default_template( $file ) {
	$file = BEANS_STRUCTURE_PATH . basename( $file );

	if ( ! file_exists( $file ) ) {
		return false;
	}

	require_once $file;

	return true;
}

/**
 * Load the fragment file.
 *
 * This function can be short-circuited using the filter event "beans_pre_load_fragment_".
 *
 * @since 1.0.0
 *
 * @param string $fragment The fragment to load. This is its filename without the extension.
 *
 * @return bool True on success, false on failure.
 */
function beans_load_fragment_file( $fragment ) {

	/**
	 * Filter to allow the child theme or plugin to short-circuit this function by passing back a `true` or
	 * truthy value.
	 *
	 * The hook's name is "beans_pre_load_fragment_" + the fragment's filename (without its extension).  For example,
	 * the header fragment's hook name is "beans_pre_load_fragment_header".
	 *
	 * @since 1.0.0
	 *
	 * @param bool Set to `true` to short-circuit this function. The default is `false`.
	 */
	if ( apply_filters( 'beans_pre_load_fragment_' . $fragment, false ) ) {
		return false;
	}

	// If fragment file does not exist, bail out.
	if ( ! file_exists( BEANS_FRAGMENTS_PATH . $fragment . '.php' ) ) {
		return false;
	}

	require_once BEANS_FRAGMENTS_PATH . $fragment . '.php';

	return true;
}

/**
 * Render the current comment's HTML markup.
 *
 * This function is a callback that is registered to {@see wp_list_comments()}.  It adds the args and depth to the
 * global comment, renders the opening <li> tag, and fires the "beans_comment" event to render the comment.
 *
 * @since 1.0.0
 *
 * @see   wp_list_comments()
 *
 * @param WP_Comment $comment Instance of the current comment, i.e. which is also the global comment.
 * @param array      $args    Array of arguments.
 * @param int        $depth   Depth of the comment in reference to its parents.
 *
 * @return void
 */
function beans_comment_callback( $comment, array $args, $depth ) {
	// To give us access, add the args and depth as public properties on the comment's global instance.
	global $comment;
	$comment->args  = $args;
	$comment->depth = $depth;

	// Render the opening <li> tag.
	$comment_class = empty( $args['has_children'] ) ? '' : 'parent';
	printf( '<li id="comment-%d" %s>',
		(int) get_comment_ID(),
		comment_class( $comment_class, $comment, null, false )
	);

	/**
	 * Render the comment's HTML markup.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_comment' );

	// The </li> tag is intentionally omitted.
}
