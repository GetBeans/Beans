<?php
/**
 * This class controls the rendering of the Beans fields for WP Customize.
 *
 * @package Beans\Framework\API\WP_Customize
 *
 * @since   1.5.0
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

/**
 * Render Beans fields content for WP Customize.
 *
 * @since   1.0.0
 *
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\WP_Customize
 */
class Beans_WP_Customize_Control extends WP_Customize_Control {

	/**
	 * Field data.
	 *
	 * @var string
	 */
	private $beans_field;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$args = func_get_args();
		call_user_func_array( array( 'parent', '__construct' ), $args );
		$this->beans_field = end( $args );
	}

	/**
	 * Field content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_content() {
		beans_field( $this->beans_field );
	}
}
