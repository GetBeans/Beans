<?php
/**
 * Handles the Beans Fields workflow.
 *
 * @ignore
 *
 * @package API\Fields
 */
final class _Beans_Fields {

	/**
	 * Fields.
	 *
	 * @type array
	 */
	private $fields = array();

	/**
	 * Fields types.
	 *
	 * @type array
	 */
	private $field_types = array();

	/**
	 * Context in which the fields are used.
	 *
	 * @type string
	 */
	private $context;

	/**
	 * Fields section.
	 *
	 * @type string
	 */
	private $section;

	/**
	 * Fields types loaded.
	 *
	 * @type array
	 */
	private static $field_types_loaded = array();

	/**
	 * Fields assets hook loaded.
	 *
	 * @type array
	 */
	private static $field_assets_hook_loaded = array();

	/**
	 * Registered fields.
	 *
	 * @type array
	 */
	private static $registered = array(
		'option'       => array(),
		'post_meta'    => array(),
		'term_meta'    => array(),
		'wp_customize' => array(),
	);

	/**
	 * Register fields.
	 */
	public function register( $fields, $context, $section ) {

		$this->fields = $fields;
		$this->context = $context;
		$this->section = $section;

		$this->add();
		$this->set_types();
		$this->do_once();
		$this->load_fields();

		add_action( 'admin_enqueue_scripts', array( $this, 'load_fields_assets_hook' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'load_fields_assets_hook' ) );

	}

	/**
	 * Register field.
	 */
	private function add() {

		$fields = array();

		foreach ( $this->fields as $field ) {
			$fields[] = $this->standardize_field( $field );
		}

		// Register fields.
		self::$registered[ $this->context ][ $this->section ] = $fields;

	}

	/**
	 * Standadrize field to beans format.
	 */
	private function standardize_field( $field ) {

		// Set defaults.
		$defaults = array(
			'label'       => false,
			'description' => false,
			'default'     => false,
			'context'     => $this->context,
			'attributes'  => array(),
			'db_group'    => false,
		);

		$field = array_merge( $defaults, $field );

		// Set field name.
		$field['name'] = 'wp_customize' == $this->context ? $field['id'] :  'beans_fields[' . $field['id'] . ']';

		if ( 'group' === $field['type'] ) {

			foreach ( $field['fields'] as $index => $_field ) {

				if ( $field['db_group'] ) {
					$_field['name'] = $field['name'] . '[' . $_field['id'] . ']';
				}

				$field['fields'][ $index ] = $this->standardize_field( $_field );

			}
		} else {

			// Add value after the standardizing the field.
			$field['value'] = $this->get_field_value( $field['id'], $field['context'], $field['default'] );

		}

		// Add required attributes for wp_customizer.
		if ( 'wp_customize' == $this->context ) {
			$field['attributes'] = array_merge(
				$field['attributes'],
				array( 'data-customize-setting-link' => $field['name'] )
			);
		}

		return $field;

	}

	/**
	 * Set the fields types used.
	 */
	private function set_types() {

		foreach ( $this->fields as $field ) {

			if ( 'group' == $field['type'] ) {
				foreach ( $field['fields'] as $_field ) {
					$this->field_types[ $_field['type'] ] = $_field['type'];
				}
			} else {
				$this->field_types[ $field['type'] ] = $field['type'];
			}
		}

	}

	/**
	 * Trigger actions only once.
	 */
	private function do_once() {

		static $once = false;

		if ( ! $once ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_global_assets' ) );

			require_once( BEANS_API_PATH . 'fields/types/field.php' );

			$once = true;

		}

	}

	/**
	 * Load the required core fields php files.
	 */
	private function load_fields() {

		foreach ( $this->field_types as $type ) {

			// Stop here if the field type has already been loaded.
			if ( in_array( $type, self::$field_types_loaded ) ) {
				continue;
			}

			$path = BEANS_API_PATH . "fields/types/{$type}.php";

			if ( file_exists( $path ) ) {
				require_once( $path );
			}

			// Set flag that field is loaded.
			self::$field_types_loaded[ $type ] = $type;

		}

	}

	/**
	 * Load the fields assets hooks. This hook can then be used to load custom fields assets.
	 */
	public function load_fields_assets_hook() {

		foreach ( $this->field_types as $type ) {

			// Stop here if the field type has already been loaded.
			if ( in_array( $type, self::$field_assets_hook_loaded ) ) {
				continue;
			}

			do_action( "beans_field_enqueue_scripts_{$type}" );

			// Set flag that field is loaded.
			self::$field_assets_hook_loaded[ $type ] = $type;

		}

	}

	/**
	 * Enqueue default fields assets.
	 */
	public function enqueue_global_assets() {

		$css = BEANS_API_URL . 'fields/assets/css/fields' . BEANS_MIN_CSS . '.css';
		$js = BEANS_API_URL . 'fields/assets/js/fields' . BEANS_MIN_CSS . '.js';

		wp_enqueue_style( 'beans-fields', $css, false, BEANS_VERSION );
		wp_enqueue_script( 'beans-fields', $js, array( 'jquery' ), BEANS_VERSION );

		do_action( 'beans_field_enqueue_scripts' );

	}

	/**
	 * Get the field value.
	 */
	private function get_field_value( $field_id, $context, $default ) {

		switch ( $context ) {

			case 'option':
				return get_option( $field_id, $default );
			break;

			case 'post_meta':
				return beans_get_post_meta( $field_id, $default );
			break;

			case 'term_meta':
				return beans_get_term_meta( $field_id, $default );
			break;

			case 'wp_customize':
				return get_theme_mod( $field_id, $default );
			break;

		}

	}

	/**
	 * Display the field content.
	 */
	public function field_content( $field ) {

		beans_open_markup_e( 'beans_field_wrap', 'div', array(
			'class' => 'bs-field-wrap bs-' . $field['type'] . ' ' . $field['context'],
		), $field );

			// Set fields loop to cater for groups.
			if ( 'group' === $field['type'] ) {
				$fields = $field['fields'];
			} else {
				$fields = array( $field );
			}

			beans_open_markup_e( 'beans_field_inside', 'div', array(
				'class' => 'bs-field-inside',
			), $fields );

				// Loop through fields.
				foreach ( $fields as $single_field ) {

					beans_open_markup_e( 'beans_field[_' . $single_field['id'] . ']', 'div', array(
						'class' => 'bs-field bs-' . $single_field['type'],
					), $single_field );

						do_action( 'beans_field_' . $single_field['type'], $single_field );

					beans_close_markup_e( 'beans_field[_' . $single_field['id'] . ']', 'div', $single_field );

				}

			beans_close_markup_e( 'beans_field_inside', 'div', $fields );

		beans_close_markup_e( 'beans_field_wrap', 'div', $field );

	}

	/**
	 * Get the registered fields.
	 */
	public function get_fields( $context, $section ) {

		if ( ! $fields = beans_get( $section, self::$registered[ $context ] ) ) {
			return false;
		}

		return $fields;

	}
}
