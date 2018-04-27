<?php
/**
 * This class provides the means to add Post Meta boxes.
 *
 * @package Beans\Framework\Api\Post_Meta
 *
 * @since 1.0.0
 */

/**
 * Handle the Beans Post Meta workflow.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Beans\Framework\API\Post_Meta
 */
final class _Beans_Post_Meta {

	/**
	 * Metabox arguments.
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Fields section.
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Constructor.
	 *
	 * @param string $section Field section.
	 * @param array  $args Arguments of the field.
	 */
	public function __construct( $section, $args ) {
		$defaults = array(
			'title'    => __( 'Undefined', 'tm-beans' ),
			'context'  => 'normal',
			'priority' => 'high',
		);

		$this->section = $section;
		$this->args    = array_merge( $defaults, $args );
		$this->do_once();

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
	}

	/**
	 * Trigger actions only once.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function do_once() {
		static $did_once = false;

		if ( $did_once ) {
			return;
		}

		add_action( 'edit_form_top', array( $this, 'render_nonce' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_filter( 'attachment_fields_to_save', array( $this, 'save_attachment' ) );

		$did_once = true;
	}

	/**
	 * Render post meta nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_nonce() {
		include dirname( __FILE__ ) . '/views/nonce.php';
	}

	/**
	 * Add the Metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Name of the post type.
	 *
	 * @return void
	 */
	public function register_metabox( $post_type ) {
		add_meta_box(
			$this->section,
			$this->args['title'],
			array( $this, 'render_metabox_content' ),
			$post_type,
			$this->args['context'],
			$this->args['priority']
		);
	}

	/**
	 * Render metabox content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_metabox_content() {

		foreach ( beans_get_fields( 'post_meta', $this->section ) as $field ) {
			beans_field( $field );
		}
	}

	/**
	 * Save Post Meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return mixed
	 */
	public function save( $post_id ) {

		if ( _beans_doing_autosave() ) {
			return false;
		}

		$fields = beans_post( 'beans_fields' );

		if ( ! $this->ok_to_save( $post_id, $fields ) ) {
			return $post_id;
		}

		foreach ( $fields as $field => $value ) {
			update_post_meta( $post_id, $field, $value );
		}
	}

	/**
	 * Save Post Meta for attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attachment Attachment data.
	 *
	 * @return mixed
	 */
	public function save_attachment( $attachment ) {

		if ( _beans_doing_autosave() ) {
			return $attachment;
		}

		$fields = beans_post( 'beans_fields' );

		if ( ! $this->ok_to_save( $attachment['ID'], $fields ) ) {
			return $attachment;
		}

		foreach ( $fields as $field => $value ) {
			update_post_meta( $attachment['ID'], $field, $value );
		}

		return $attachment;
	}

	/**
	 * Check if all criteria are met to safely save post meta.
	 *
	 * @param int   $id The Post Id.
	 * @param array $fields The array of fields to save.
	 *
	 * @return bool
	 */
	public function ok_to_save( $id, $fields ) {
		if ( ! wp_verify_nonce( beans_post( 'beans_post_meta_nonce' ), 'beans_post_meta_nonce' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $id ) ) {
			return false;
		}

		return ! empty( $fields );
	}
}
