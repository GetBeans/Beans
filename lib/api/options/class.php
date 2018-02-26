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
final class _Beans_Options {

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
	 * Register the Metabox.
	 *
	 * @since 1.0.0
	 * @ignore
	 * @access private
	 *
	 * @return void
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
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function metabox_content() {

		foreach ( beans_get_fields( 'option', $this->section ) as $field ) {
			beans_field( $field );
		}
	}

	/**
	 * Page content.
	 *
	 * @since 1.0.0
	 *
	 * @param int $page Page ID.
	 *
	 * @return void
	 */
	public function page( $page ) {
		global $wp_meta_boxes;

		$boxes = beans_get( $page, $wp_meta_boxes );

		if ( ! $boxes ) {
			return;
		}

		// Only add a column class if there is more than 1 metabox.
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
	 *
	 * @since 1.0.0
	 *
	 * @return void
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

		if ( ! ( $fields ) ) {
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

		if ( ! ( $fields ) ) {
			return false;
		}

		foreach ( $fields as $field => $value ) {
			delete_option( $field );
		}

		$this->success = true;
	}

	/**
	 * Save notice content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_notices() {

		if ( $this->success ) {
			?>
			<div id="message" class="updated">
				<p><?php esc_html_e( 'Settings saved successfully!', 'tm-beans' ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div id="message" class="error">
				<p><?php esc_html_e( 'Settings could not be saved, please try again.', 'tm-beans' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Reset notice content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function reset_notices() {

		if ( $this->success ) {
			?>
			<div id="message" class="updated">
				<p><?php esc_html_e( 'Settings reset successfully!', 'tm-beans' ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div id="message" class="error">
				<p><?php esc_html_e( 'Settings could not be reset, please try again.', 'tm-beans' ); ?></p>
			</div>
			<?php
		}
	}
}
