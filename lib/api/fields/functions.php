<?php
/**
 * The Beans Fields component offers a range of fields which can be used in the WordPress admin.
 *
 * Fields can be used as Options, Post Meta, Term Meta or WP Customizer Options. Custom fields can easily be added, too.
 *
 * @package Beans\Framework\API\Fields
 */

/**
 * Register the given fields.
 *
 * This function should only be invoked through the 'admin_init' action.
 *
 * @since 1.0.0
 *
 * @param array  $fields      {
 *      Array of fields to register.
 *
 *      @type string  $id          A unique id used for the field. This id will also be used to save the value in the
 *                                 database.
 *      @type string  $type        The type of field to use. Please refer to the Beans core field types for more
 *                                 information. Custom field types are accepted here.
 *      @type string  $label       The field label. Default false.
 *      @type string  $description The field description. The description can be truncated using <!--more--> as a
 *                                 delimiter. Default false.
 *      @type array   $attributes  An array of attributes to add to the field. The array key defines the attribute name
 *                                 and the array value defines the attribute value. Default array.
 *      @type mixed   $default     The default field value. Default false.
 *      @type array   $fields      Must only be used for the 'group' field type. The array arguments are similar to the
 *                                 {@see beans_register_fields()} $fields arguments.
 *      @type bool    $db_group    Must only be used for the 'group' field type. It defines whether the group of fields
 *                                 should be saved as a group or as individual entries in the database. Default false.
 * }
 * @param string $context     The context in which the fields are used. 'option' for options/settings pages,
 *                            'post_meta' for post fields, 'term_meta' for taxonomies fields and 'wp_customize' for WP
 *                            customizer fields.
 * @param string $section     A section ID to define the group of fields.
 *
 * @return bool True on success, false on failure.
 */
function beans_register_fields( array $fields, $context, $section ) {

	if ( empty( $fields ) ) {
		return false;
	}

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once BEANS_API_PATH . 'fields/class-beans-fields.php';

	$class = new _Beans_Fields();
	return $class->register( $fields, $context, $section );
}

/**
 * Get the registered fields.
 *
 * This function is used to get the previously registered fields in order to display them using
 * {@see beans_field()}.
 *
 * @since 1.0.0
 *
 * @param string      $context The context in which the fields are used. 'option' for options/settings pages,
 *                             'post_meta' for post fields, 'term_meta' for taxonomies fields and 'wp_customize' for WP
 *                             customizer fields.
 * @param string|bool $section Optional. A section ID to define a group of fields. This is mostly used for metaboxes
 *                             and WP Customizer sections.
 *
 * @return array|bool Array of registered fields on success, false on failure.
 */
function beans_get_fields( $context, $section = false ) {

	if ( ! class_exists( '_Beans_Fields' ) ) {
		return;
	}

	return _Beans_Fields::get_fields( $context, $section );
}

/**
 * Render (echo) a field.
 *
 * This function echos the field content. Must be used in the loop of fields obtained using
 * {@see beans_get_fields()}.
 *
 * @since 1.0.0
 * @since 1.5.0 Moved rendering code out of _Beans_Fields.
 *
 * @param array $field The given field to render, obtained using {@see beans_get_fields()}.
 *
 * @return void
 */
function beans_field( array $field ) {

	if ( ! class_exists( '_Beans_Fields' ) ) {
		return;
	}

	$group_field_type = 'group' === $field['type'];

	beans_open_markup_e( 'beans_field_wrap', 'div', array(
		'class' => 'bs-field-wrap bs-' . $field['type'] . ' ' . $field['context'],
	), $field );

	// Set fields loop to cater for groups.
	if ( $group_field_type ) {
		$fields = $field['fields'];
	} else {
		$fields = array( $field );
	}

	beans_open_markup_e( 'beans_field_inside', 'div', array(
		'class' => 'bs-field-inside',
	), $fields );

	if ( $group_field_type ) {
		beans_open_markup_e( 'beans_field_group_fieldset', 'fieldset', array(
			'class' => 'bs-field-fieldset',
		), $field );
			beans_open_markup_e( 'beans_field_group_legend', 'legend', array(
				'class' => 'bs-field-legend',
			), $field );
				echo esc_html( $field['label'] );
			beans_close_markup_e( 'beans_field_group_legend', 'legend', $field );
	}

	// Loop through fields.
	foreach ( $fields as $single_field ) {
		beans_open_markup_e( 'beans_field[_' . $single_field['id'] . ']', 'div', array(
			'class' => 'bs-field bs-' . $single_field['type'],
		), $single_field );

		if ( $group_field_type ) {
			/**
			 * Fires the "beans_field_group_label" event to render this field's label.
			 *
			 * @since 1.5.0
			 *
			 * @param array $single_field The given single field.
			 */
			do_action( 'beans_field_group_label', $single_field );
		}

		/**
		 * Fires the "beans_field_{type}" event to render this single field.
		 *
		 * @since 1.5.0
		 *
		 * @param array $single_field The given single field.
		 */
		do_action( 'beans_field_' . $single_field['type'], $single_field );

		beans_close_markup_e( 'beans_field[_' . $single_field['id'] . ']', 'div', $single_field );
	}

	if ( $group_field_type ) {
		beans_close_markup_e( 'beans_field_group_fieldset', 'fieldset', $field );
	}
	beans_close_markup_e( 'beans_field_inside', 'div', $fields );
	beans_close_markup_e( 'beans_field_wrap', 'div', $field );
}

/**
 * Pre-standardize the fields by keying each field by its ID.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param array $fields An array of fields to be standardized.
 *
 * @return array
 */
function _beans_pre_standardize_fields( array $fields ) {
	$_fields = array();

	foreach ( $fields as $field ) {
		$_fields[ $field['id'] ] = $field;

		if ( 'group' === beans_get( 'type', $field ) ) {
			$_fields[ $field['id'] ]['fields'] = _beans_pre_standardize_fields( $field['fields'] );
		}
	}

	return $_fields;
}
