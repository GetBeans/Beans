<?php
/**
 * The Beans Image component contains a set of functions to edit images on the fly.
 *
 * Edited images are duplicates of the originals. All modified images are stored in a shared folder,
 * which makes it easy to delete them without impacting the originals.
 *
 * @package Beans\Framework\API\Image
 */

/**
 * Edit image size and/or quality.
 *
 * Edited images are duplicates of the originals. All modified images are stored in a shared folder,
 * which makes it easy to delete them without impacting the originals.
 *
 * @since 1.0.0
 *
 * @param string $src         The image source.
 * @param array  $args        An array of editor arguments, where the key is the {@see WP_Image_Editor} method name
 *                            and the value is a numeric array of arguments for the method. Make sure to specify
 *                            all of the arguments the WordPress editor's method requires. Refer to
 *                            {@link https://codex.wordpress.org/Class_Reference/WP_Image_Editor#Methods} for more
 *                            information on the available methods and each method's arguments.
 * @param string $output      Optional. Returned format. Accepts STRING, OBJECT, ARRAY_A, or ARRAY_N.
 *                            Default is STRING.
 *
 * @return string|array Image source if output set the STRING, image data otherwise.
 */
function beans_edit_image( $src, array $args, $output = 'STRING' ) {
	require_once BEANS_API_PATH . 'image/class-beans-image-editor.php';
	$editor = new Beans_Image_Editor( $src, $args, $output );
	return $editor->run();
}

/**
 * Get attachment data.
 *
 * This function regroups all necessary data about a post attachment into an object.
 *
 * @since 1.0.0
 *
 * @param string $post_id The post id.
 * @param string $size    Optional. The desired attachment size. Accepts 'thumbnail', 'medium', 'large'
 *                        or 'full'.
 *
 * @return object Post attachment data.
 */
function beans_get_post_attachment( $post_id, $size = 'full' ) {
	$id   = get_post_thumbnail_id( $post_id );
	$post = get_post( $id );
	$src  = wp_get_attachment_image_src( $id, $size );

	$obj              = new stdClass();
	$obj->id          = $id;
	$obj->src         = $src[0];
	$obj->width       = $src[1];
	$obj->height      = $src[2];
	$obj->alt         = trim( strip_tags( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ); //phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- We want to strip the tags and keep the content.
	$obj->title       = $post->post_title;
	$obj->caption     = $post->post_excerpt;
	$obj->description = $post->post_content;

	return $obj;
}

/**
 * Edit post attachment.
 *
 * This function is shortcut of {@see beans_edit_image()}. It should be used to edit a post attachment.
 *
 * @since 1.0.0
 *
 * @param string $post_id     The post id.
 * @param array  $args        An array of editor arguments, where the key is the {@see WP_Image_Editor} method name
 *                            and the value is a numeric array of arguments for the method. Make sure to specify
 *                            all of the arguments the WordPress editor's method requires. Refer to
 *                            {@link https://codex.wordpress.org/Class_Reference/WP_Image_Editor#Methods} for more
 *                            information on the available methods and each method's arguments.
 *
 * @return object Edited post attachment data.
 */
function beans_edit_post_attachment( $post_id, $args = array() ) {

	if ( ! has_post_thumbnail( $post_id ) ) {
		return false;
	}

	// Get full size image.
	$attachment = beans_get_post_attachment( $post_id, 'full' );
	$edited     = beans_edit_image( $attachment->src, $args, 'ARRAY_A' );

	if ( ! $edited ) {
		return $attachment;
	}

	return (object) array_merge( (array) $attachment, $edited );
}

/**
 * Get the "edited images" storage directory, i.e. where the "edited images" are/will be stored.
 *
 * @since 1.0.0
 *
 * @return string
 */
function beans_get_images_dir() {
	$wp_upload_dir = wp_upload_dir();

	/**
	 * Filter the edited images directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string Default path to the Beans' edited images storage directory.
	 */
	$dir = apply_filters( 'beans_images_dir', trailingslashit( $wp_upload_dir['basedir'] ) . 'beans/images/' );

	return wp_normalize_path( trailingslashit( $dir ) );
}

add_action( 'beans_loaded_api_component_image', 'beans_add_image_options_to_settings' );
/**
 * Add the "image options" to the Beans Settings page.
 *
 * @since 1.5.0
 *
 * @return Beans_Image_Options|void
 */
function beans_add_image_options_to_settings() {

	if ( ! class_exists( 'Beans_Image_Options' ) ) {
		return;
	}

	$instance = new Beans_Image_Options();
	$instance->init();

	return $instance;
}
