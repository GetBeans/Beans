<?php
/**
 * Handle the Beans Options workflow.
 *
 * @ignore
 *
 * @package API\Options
 */
final class _Beans_Options {

	/**
	 * Metabox arguments.
	 *
	 * @type array
	 */
	private $args = array();

	/**
	 * Form submission status.
	 *
	 * @type bool
	 */
	private $success = false;

	/**
	 * Fields section.
	 *
	 * @type string
	 */
	private $section;

	/**
	 * Register options.
	 */
	public function register( $section, $args ) {

		$defaults = array(
			'title'   => __( 'Undefined', 'tm-beans' ),
			'context' => 'normal',
		);

		$this->section = $section;
		$this->args = array_merge( $defaults, $args );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		$this->register_metabox();

	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_assets() {

		wp_enqueue_script( 'postbox' );

	}

	/**
	 * Register the Metabox.
	 */
	private function register_metabox() {

		add_meta_box(
			$this->section,
			$this->args['title'],
			array( $this, 'metabox_content' ),
			beans_get( 'page' ),
			$this->args['context'],
			'default'
		);

	}

	/**
	 * Metabox content.
	 */
	public function metabox_content() {

		foreach ( beans_get_fields( 'option', $this->section ) as $field ) {
			beans_field( $field );
		}

	}

	/**
	 * Page content.
	 */
	public function page( $page ) {

		global $wp_meta_boxes;

		if ( ! $boxes = beans_get( $page, $wp_meta_boxes ) ) {
			return;
		}

		// Only add column class if there is more than 1 metaboxes.
		$column_class = beans_get( 'column', $boxes, array() ) ? ' column' : false;

		// Set page data which will be used by the postbox.
		?>
		<form action="" method="post" class="bs-options" data-page="<?php echo esc_attr( beans_get( 'page' ) ); ?>">
			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			<input type="hidden" name="beans_options_nonce" value="<?php echo esc_attr( wp_create_nonce( 'beans_options_nonce' ) ); ?>" />
			<div class="metabox-holder<?php echo esc_attr( $column_class ); ?>">
				<?php
				do_meta_boxes( $page, 'normal', null );

				if ( $column_class ) {
					do_meta_boxes( $page, 'column', null );
				}
				?>
			</div>
			<p class="bs-options-form-actions">
				<input type="submit" name="beans_save_options" value="<?php echo esc_attr__( 'Save', 'tm-beans' ); ?>" class="button-primary">
				<input type="submit" name="beans_reset_options" value="<?php echo esc_attr__( 'Reset', 'tm-beans' ); ?>" class="button-secondary">
			</p>
		</form>
		<?php

	}

	/**
	 * Form actions.
	 */
	public function actions() {

		if ( beans_post( 'beans_save_options' ) ) {

			$this->save();
			add_action( 'admin_notices', array( $this, 'save_notices' ) );

		}

		if ( beans_post( 'beans_reset_options' ) ) {

			$this->reset();
			add_action( 'admin_notices', array( $this, 'reset_notices' ) );

		}

	}

	/**
	 * Save options.
	 */
	private function save() {

		if ( ! wp_verify_nonce( beans_post( 'beans_options_nonce' ), 'beans_options_nonce' ) ) {
			return false;
		}

		if ( ! ( $fields = beans_post( 'beans_fields' ) ) ) {
			return false;
		}

		foreach ( $fields as $field => $value ) {
			update_option( $field, stripslashes_deep( $value ) );
		}

		$this->success = true;

	}

	/**
	 * Reset options.
	 */
	private function reset() {

		if ( ! wp_verify_nonce( beans_post( 'beans_options_nonce' ), 'beans_options_nonce' ) ) {
			return false;
		}

		if ( ! ( $fields = beans_post( 'beans_fields' ) ) ) {
			return false;
		}

		foreach ( $fields as $field => $value ) {
			delete_option( $field );
		}

		$this->success = true;

	}

	/**
	 * Save notice content.
	 */
	public function save_notices() {

		if ( $this->success ) {
			?>
			<div id="message" class="updated">
				<p><?php _e( 'Settings saved successfully!', 'tm-beans' ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div id="message" class="error">
				<p><?php _e( 'Settings could not be saved, please try again.', 'tm-beans' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Reset notice content.
	 */
	public function reset_notices() {

		if ( $this->success ) {
			?>
			<div id="message" class="updated">
				<p><?php _e( 'Settings reset successfully!', 'tm-beans' ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div id="message" class="error">
				<p><?php _e( 'Settings could not be reset, please try again.', 'tm-beans' ); ?></p>
			</div>
			<?php
		}

	}
}
