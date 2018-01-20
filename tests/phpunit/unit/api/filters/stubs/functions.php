<?php
/**
 * Stubbed functions for the Filters API tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Filters\Stubs
 *
 * @since   1.5.0
 */

/**
 * Filter callback for "the_title" filter event.
 *
 * @since 1.5.0
 *
 * @param string     $post_title The post's title.
 * @param int|string $post_id    ID of the post.
 *
 * @return string
 */
function beans_test_the_content( $post_title, $post_id ) {
	return $post_title . '_' . $post_id;
}

if ( ! function_exists( 'beans_modify_widget_count' ) ) {
	/**
	 * Modify widget count.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	function beans_modify_widget_count() {
		return 20;
	}
}

if ( ! function_exists( 'beans_loop_query_args_base' ) ) {
	/**
	 * Modify the Bean's loop query arguments.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	function beans_loop_query_args_base() {
		return array( 'base' );
	}
}

if ( ! function_exists( 'beans_loop_query_args_main' ) ) {
	/**
	 * Modify the Bean's loop query arguments. Callback for the sub-hook.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args The query's arguments.
	 *
	 * @return array
	 */
	function beans_loop_query_args_main( array $args ) {
		$args[] = '_main';

		return $args;
	}
}
