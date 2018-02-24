<?php
/**
 * Handles standardizing and registering the fields into the Beans Fields' Container.
 *
 * @package Beans\Framework\API\Fields
 *
 * @since   1.0.0
 */

/**
 * The Beans Fields' Container.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Fields
 */
final class _Beans_Fields {

	/**
	 * Fields.
	 *
	 * @var array
	 */
	private $fields = array();

	/**
	 * Field types.
	 *
	 * @var array
	 */
	private $field_types = array();

	/**
	 * Context in which the fields are used.
	 *
	 * @var string
	 */
	private $context;

	/**
	 * Field section.
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Field types loaded.
	 *
	 * @var array
	 */
	private static $field_types_loaded = array();

	/**
	 * Field assets hook loaded.
	 *
	 * @var array
	 */
	private static $field_assets_hook_loaded = array();

	/**
	 * Registered fields.
	 *
	 * @var array
	 */
	private static $registered = array(
		'option'       => array(),
		'post_meta'    => array(),
		'term_meta'    => array(),
		'wp_customize' => array(),
	);

	/**
	 * Register the given fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $fields      Array of fields to register.
	 * @param string $context     The context in which the fields are used. 'option' for options/settings pages,
	 *                            'post_meta' for post fields, 'term_meta' for taxonomies fields and 'wp_customize' for
	 *                            WP customizer fields.
	 * @param string $section     A section ID to define the group of fields.
	 *
	 * @return bool
	 */
	public function register( array $fields, $context, $section ) {
		$this->fields  = $fields;
		$this->context = $context;
		$this->section = $section;

		$this->add();
		$this->do_once();
		$this->load_fields();

		add_action( 'admin_enqueue_scripts', array( $this, 'load_fields_assets_hook' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'load_fields_assets_hook' ) );

		return true;
	}

	/**
	 * Register the field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function add() {
		$fields = array();

		foreach ( $this->fields as $field ) {
			$fields[] = $this->standardize_field( $field );
			$this->set_type( $field );
		}

		// Register fields.
		self::$registered[ $this->context ][ $this->section ] = $fields;
	}

	/**
	 * Standardize the field to include the default configuration parameters and fetching the current value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field The given field to be standardized.
	 *
	 * @return array
	 */
	private function standardize_field( array $field ) {
		$field = array_merge( array(
			'label'       => false,
			'description' => false,
			'default'     => false,
			'context'     => $this->context,
			'attributes'  => array(),
			'db_group'    => false,
		), $field );

		// Set the field's name.
		$field['name'] = 'wp_customize' === $this->context ? $field['id'] : 'beans_fields[' . $field['id'] . ']';

		if ( 'group' === $field['type'] ) {

			foreach ( $field['fields'] as $index => $_field ) {

				if ( $field['db_group'] ) {
					$_field['name'] = $field['name'] . '[' . $_field['id'] . ']';
				}

				$field['fields'][ $index ] = $this->standardize_field( $_field );
			}
		} else {
			// Add value after standardizing the field.
			$field['value'] = $this->get_field_value( $field['id'], $field['context'], $field['default'] );
		}

		// Add required attributes for wp_customizer.
		if ( 'wp_customize' === $this->context ) {
			$field['attributes'] = array_merge(
				$field['attributes'],
				array( 'data-customize-setting-link' => $field['name'] )
			);
		}

		return $field;
	}

	/**
	 * Set the type for the given field.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field The given field.
	 *
	 * @return void
	 */
	private function set_type( array $field ) {

		// Set the single field's type.
		if ( 'group' !== $field['type'] ) {
			$this->field_types[ $field['type'] ] = $field['type'];
			return;
		}

		foreach ( $field['fields'] as $_field ) {
			$this->field_types[ $_field['type'] ] = $_field['type'];
		}
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

		if ( $once ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );

		// Load the field label and description handler.
		require_once BEANS_API_PATH . 'fields/types/field.php';

		$once = true;
	}

	/**
	 * Load the field type PHP file for each of the fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load_fields() {

		foreach ( $this->field_types as $type ) {

			// Stop here if the field type has already been loaded.
			if ( in_array( $type, self::$field_types_loaded, true ) ) {
				continue;
			}

			$path = BEANS_API_PATH . "fields/types/{$type}.php";

			if ( file_exists( $path ) ) {
				require_once $path;
			}

			// Set a flag that the field is loaded.
			self::$field_types_loaded[ $type ] = $type;
		}
	}

	/**
	 * Load the field's assets hook. This hook can then be used to load custom assets for the field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_fields_assets_hook() {

		foreach ( $this->field_types as $type ) {

			// Stop here if the field type has already been loaded.
			if ( in_array( $type, self::$field_assets_hook_loaded, true ) ) {
				continue;
			}

			do_action( "beans_field_enqueue_scripts_{$type}" );

			// Set a flag that the field is loaded.
			self::$field_assets_hook_loaded[ $type ] = $type;
		}
	}

	/**
	 * Enqueue the default assets for the fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_global_assets() {
		$css = BEANS_API_URL . 'fields/assets/css/fields' . BEANS_MIN_CSS . '.css';
		$js  = BEANS_API_URL . 'fields/assets/js/fields' . BEANS_MIN_CSS . '.js';

		wp_enqueue_style( 'beans-fields', $css, false, BEANS_VERSION );
		wp_enqueue_script( 'beans-fields', $js, array( 'jquery' ), BEANS_VERSION );

		do_action( 'beans_field_enqueue_scripts' );
	}

	/**
	 * Get the field value.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Return the default when the context is not pre-defined.
	 *
	 * @param string $field_id Field's ID.
	 * @param string $context  The field's context, i.e. "option", "post_meta", "term_meta", or "wp_customize".
	 * @param mixed  $default  The field's default value.
	 *
	 * @return mixed|string|void
	 */
	private function get_field_value( $field_id, $context, $default ) {

		switch ( $context ) {

			case 'option':
				return get_option( $field_id, $default );

			case 'post_meta':
				return beans_get_post_meta( $field_id, $default );

			case 'term_meta':
				return beans_get_term_meta( $field_id, $default );

			case 'wp_customize':
				return get_theme_mod( $field_id, $default );
		}

		return $default;
	}

	/**
	 * Get the registered fields.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Changed to static method.
	 *
	 * @param string $context The context in which the fields are used. 'option' for options/settings pages,
	 *                        'post_meta' for post fields, 'term_meta' for taxonomies fields and 'wp_customize' for WP
	 *                        customizer fields.
	 * @param string $section Optional. A section ID to define a group of fields. This is mostly used for meta boxes
	 *                        and WP Customizer sections.
	 *
	 * @return array|bool Array of registered fields on success, false on failure.
	 */
	public static function get_fields( $context, $section ) {
		$fields = beans_get( $section, self::$registered[ $context ] );

		if ( ! $fields ) {
			return false;
		}

		return $fields;
	}
}
