<?php
/**
 * This class provides the means to handle the Beans Options workflow.
 *
 * @package Beans\Framework\API\Options
 *
 * @since 1.0.0
 */

/**
 * Handle the Beans Options workflow.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Beans\Framework\API\Options
 */
final class Beans_Options {

	/**
	 * Metabox arguments.
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Form submission status.
	 *
	 * @var bool
	 */
	private $success = false;

	/**
	 * Field section.
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Register options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Section of the field.
	 * @param array  $args Arguments of the option.
	 *
	 * @return void
	 */
	public function register( $section, $args ) {
		$defaults = array(
			'title'   => __( 'Undefined', 'tm-beans' ),
			'context' => 'normal',
		);

		$this->section = $section;
		$this->args    = array_merge( $defaults, $args );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		$this->register_metabox();
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'postbox' );
	}

	/**
	 * Register the Metabox with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function register_metabox() {
		add_meta_box(
			$this->section,
			$this->args['title'],
			array( $this, 'render_metabox' ),
			beans_get( 'page' ),
			$this->args['context'],
			'default'
		);
	}

	/**
	 * Render the metabox's content. The callback is fired by WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_metabox() {
		$fields = beans_get_fields( 'option', $this->section );

		if ( empty( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			beans_field( $field );
		}
	}

	/**
	 * Render the page's (screen's) content.
	 *
	 * @since 1.0.0
	 *
	 * @param string|WP_Screen $page The given page.
	 *
	 * @return void
	 */
	public function render_page( $page ) {
		global $wp_meta_boxes;

		$boxes = beans_get( $page, $wp_meta_boxes );

		if ( ! $boxes ) {
			return;
		}

		// Only add a column class if there is more than 1 metabox.
		$column_class = beans_get( 'column', $boxes, array() ) ? ' column' : false;

		include dirname( __FILE__ ) . '/views/page.php';
	}

	/**
	 * Process the form's actions.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function process_actions() {

		if ( beans_post( 'beans_save_options' ) ) {
			$this->save();
			add_action( 'admin_notices', array( $this, 'render_save_notice' ) );
		}

		if ( beans_post( 'beans_reset_options' ) ) {
			$this->reset();
			add_action( 'admin_notices', array( $this, 'render_reset_notice' ) );
		}
	}

	/**
	 * Save options.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function save() {

		if ( ! wp_verify_nonce( beans_post( 'beans_options_nonce' ), 'beans_options_nonce' ) ) {
			return false;
		}

		$fields = beans_post( 'beans_fields' );

		if ( ! $fields ) {
			return false;
		}

		foreach ( $fields as $field => $value ) {
			update_option( $field, stripslashes_deep( $value ) );
		}

		$this->success = true;
	}

	/**
	 * Reset options.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function reset() {

		if ( ! wp_verify_nonce( beans_post( 'beans_options_nonce' ), 'beans_options_nonce' ) ) {
			return false;
		}

		$fields = beans_post( 'beans_fields' );

		if ( ! $fields ) {
			return false;
		}

		foreach ( $fields as $field => $value ) {
			delete_option( $field );
		}

		$this->success = true;
	}

	/**
	 * Render the save notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_save_notice() {

		if ( $this->success ) {
			include dirname( __FILE__ ) . '/views/save-notice-success.php';
			return;
		}

		include dirname( __FILE__ ) . '/views/save-notice-error.php';
	}

	/**
	 * Render the reset notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_reset_notice() {

		if ( $this->success ) {
			include dirname( __FILE__ ) . '/views/reset-notice-success.php';
			return;
		}

		include dirname( __FILE__ ) . '/views/reset-notice-error.php';
	}
}
