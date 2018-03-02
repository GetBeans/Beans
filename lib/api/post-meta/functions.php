<?php
/**
 * @package API\Post_Meta
 */

/**
 * Get the current post meta value.
 *
 * This function is a shortcut of {@link http://codex.wordpress.org/Function_Reference/get_post_meta get_post_meta()}.
 *
 * @since 1.0.0
 *
 * @param string $field_id The post meta id searched.
 * @param mixed  $default  Optional. The default value to return of the post meta value doesn't exist.
 * @param int    $post_id  Optional. Overwrite the current post id.
 *
 * @return mixed Saved data if exist, otherwise default value set.
 */
function beans_get_post_meta( $field_id, $default = false, $post_id = false ) {

	if ( ! $post_id ) {
		$post_id = ! ( $id = get_the_id() ) ? beans_get( 'post' ): $id;
	}

	$post_meta = get_post_meta( $post_id );

	if ( isset( $post_meta[ $field_id ] ) ) {
		return get_post_meta( $post_id, $field_id, true );
	}

	return $default;

}
