<?php
/**
 * Functions for post meta.
 *
 * @package Beans\Framework\API\Post_Meta
 *
 * @since   1.0.0
 */

/**
 * Get the post's meta value.  When no post ID is given, get the current post's meta value.
 *
 * This function is a shortcut of {@link http://codex.wordpress.org/Function_Reference/get_post_meta get_post_meta()}.
 *
 * @since 1.0.0
 *
 * @param string           $meta_key The post meta id searched.
 * @param mixed            $default  Optional. The default value to return of the post meta value doesn't exist.
 * @param int|string|false $post_id  Optional. Overwrite the current post id.
 *
 * @return mixed Saved data if exist, otherwise default value set.
 */
function beans_get_post_meta( $meta_key, $default = false, $post_id = false ) {

	if ( ! $post_id ) {
		$id      = get_the_id();
		$post_id = ! $id ? beans_get( 'post' ) : $id;
	}

	$post_meta = get_post_meta( $post_id );

	if ( isset( $post_meta[ $meta_key ] ) ) {
		return get_post_meta( $post_id, $meta_key, true );
	}

	return $default;
}
