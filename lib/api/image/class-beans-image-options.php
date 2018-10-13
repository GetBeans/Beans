<?php
/**
 * This class handles adding the Beans' Image options to the Beans' Settings page.
 *
 * @package Beans\Framework\Api\Image
 *
 * @since   1.0.0
 */

/**
 * Beans Image Options Handler.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Image
 */
final class _Beans_Image_Options {

	/**
	 * Initialize the hooks.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function init() {
		// Load with priority 15 so that we can check if other Beans metaboxes exist.
		add_action( 'admin_init', array( $this, 'register' ), 15 );
		add_action( 'admin_init', array( $this, 'flush' ), - 1 );
		add_action( 'admin_notices', array( $this, 'render_success_notice' ) );
		add_action( 'beans_field_flush_edited_images', array( $this, 'render_flush_button' ) );
	}

	/**
	 * Register the options.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function register() {
		return beans_register_options(
			$this->get_fields_to_register(),
			'beans_settings',
			'images_options',
			array(
				'title'   => __( 'Images options', 'tm-beans' ),
				'context' => $this->has_metaboxes() ? 'column' : 'normal',
			)
		);
	}

	/**
	 * Get the fields to register.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	private function get_fields_to_register() {
		return require dirname( __FILE__ ) . '/config/fields.php';
	}

	/**
	 * Checks if there are metaboxes registered already.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	private function has_metaboxes() {
		global $wp_meta_boxes;

		$metaboxes = beans_get( 'beans_settings', $wp_meta_boxes );
		return ! empty( $metaboxes );
	}

	/**
	 * Flush images from the Beans cached folder.
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
	 * Renders the success admin notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_success_notice() {

		if ( ! beans_post( 'beans_flush_edited_images' ) ) {
			return;
		}

		include dirname( __FILE__ ) . '/views/flushed-notice.php';
	}

	/**
	 * Render the flush button, which is used to flush the images' cache.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Registered options.
	 *
	 * @return void
	 */
	public function render_flush_button( $field ) {

		if ( 'beans_edited_images_directories' !== $field['id'] ) {
			return;
		}

		include dirname( __FILE__ ) . '/views/flush-button.php';
	}
}
