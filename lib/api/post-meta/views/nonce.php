<?php
/**
 * View file for the post meta nonce.
 *
 * @package Beans\Framework\API\Post_Meta
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>
<input type="hidden" name="beans_post_meta_nonce" value="<?php echo esc_attr( wp_create_nonce( 'beans_post_meta_nonce' ) ); ?>" />
