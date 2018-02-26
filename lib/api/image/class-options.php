<?php
/**
 * This class provides the means to add image options.
 *
 * @package Beans\Framework\Api\Image
 *
 * @since 1.0.0
 */

/**
 * Beans image options.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Beans\Framework\API\Image
 */
final class _Beans_Image_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Load with priority 15 so that we can check if other Beans metaboxes exist.
		add_action( 'admin_init', array( $this, 'register' ), 15 );
		add_action( 'admin_init', array( $this, 'flush' ), -1 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		add_action( 'beans_field_flush_edited_images', array( $this, 'option' ) );
	}

	/**
	 * Register options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		global $wp_meta_boxes;

		$fields = array(
			array(
				'id'          => 'beans_edited_images_directories',
				'type'        => 'flush_edited_images',
				'description' => __( 'Clear all edited images. New images will be created on page load.', 'tm-beans' ),
			),
		);

		beans_register_options( $fields, 'beans_settings', 'images_options', array(
			'title'   => __( 'Images options', 'tm-beans' ),
			'context' => beans_get( 'beans_settings', $wp_meta_boxes ) ? 'column' : 'normal', // Check of other beans boxes.
		) );
	}

	/**
	 * Flush images for all folders set.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function flush() {

		if ( ! beans_post( 'beans_flush_edited_images' ) ) {
			return;
		}

		beans_remove_dir( beans_get_images_dir() );
	}

	/**
	 * Image editor notice notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_notice() {

		if ( ! beans_post( 'beans_flush_edited_images' ) ) {
			return;
		}

		?>
		<div id="message" class="updated"><p><?php esc_html_e( 'Images flushed successfully!', 'tm-beans' ); ?></p></div>
		<?php
	}

	/**
	 * Add a button to flush images.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Metabox settings.
	 *
	 * @return void
	 */
	public function option( $field ) {

		if ( 'beans_edited_images_directories' !== $field['id'] ) {
			return;
		}

		?>
		<input type="submit" name="beans_flush_edited_images" value="<?php esc_html_e( 'Flush images', 'tm-beans' ); ?>" class="button-secondary" />
		<?php
	}
}

new _Beans_Image_Options();
