<?php
/**
 * Stubbed functions for the Filters API tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Filters\Stubs
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
