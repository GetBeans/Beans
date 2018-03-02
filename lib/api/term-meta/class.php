<?php
/**
 * Class to handle the Beans Term Meta Workflow.
 *
 * @package Beans\Framework\API\Term_Meta
 *
 * @since 1.0.0
 */

/**
 * Handle the Beans Term Meta workflow.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Beans\Framework\API\Term_Meta
 */
final class _Beans_Term_Meta {

	/**
	 * Field section.
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Constructor.
	 *
	 * @param string $section Field section.
	 */
	public function __construct( $section ) {
		$this->section = $section;
		$this->do_once();
		add_action( beans_get( 'taxonomy' ) . '_edit_form_fields', array( $this, 'fields' ) );
	}

	/**
	 * Trigger actions only once.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function do_once() {
		static $once = false;

		if ( ! $once ) {
			add_action( beans_get( 'taxonomy' ) . '_edit_form', array( $this, 'nonce' ) );
			add_action( 'edit_term', array( $this, 'save' ) );
			add_action( 'delete_term', array( $this, 'delete' ), 10, 3 );

			$once = true;
		}
	}

	/**
	 * Term meta nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function nonce() {
	?>
	<input type="hidden" name="beans_term_meta_nonce" value="<?php echo esc_attr( wp_create_nonce( 'beans_term_meta_nonce' ) ); ?>" />
	<?php
	}

	/**
	 * Fields content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function fields() {
		beans_remove_action( 'beans_field_label' );
		beans_modify_action_hook( 'beans_field_description', 'beans_field_wrap_after_markup' );
		beans_modify_markup( 'beans_field_description', 'p' );
		beans_add_attribute( 'beans_field_description', 'class', 'description' );

		foreach ( beans_get_fields( 'term_meta', $this->section ) as $field ) {
			?>
			<tr class="form-field">
				<th scope="row">
					<?php echo esc_html( beans_field_label( $field ) ); ?>
				</th>
				<td>
					<?php echo esc_html( beans_field( $field ) ); ?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Save Term Meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id Term ID.
	 *
	 * @return int
	 */
	public function save( $term_id ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $term_id;
		}

		if ( ! wp_verify_nonce( beans_post( 'beans_term_meta_nonce' ), 'beans_term_meta_nonce' ) ) {
			return $term_id;
		}

		$fields = beans_post( 'beans_fields' );

		if ( ! $fields ) {
			return $term_id;
		}

		foreach ( $fields as $field => $value ) {
			update_option( "beans_term_{$term_id}_{$field}", stripslashes_deep( $value ) );
		}
	}

	/**
	 * Delete Term Meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id Term ID.
	 *
	 * @return void
	 */
	public function delete( $term_id ) {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
				"beans_term_{$term_id}_%"
			)
		);
	}
}
