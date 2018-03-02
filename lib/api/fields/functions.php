<?php
/**
 * The Beans Fields component offers a range of fields which can be used in the WordPress admin.
 *
 * Fields can be used as Options, Post Meta, Term Meta or WP
 * Customizer Options. Custom fields can easily be added too.
 *
 * @package API\Fields
 */

/**
 * Register fields.
 *
 * This function should only be invoked through the 'admin_init' action.
 *
 * @since 1.0.0
 *
 * @param array  $fields {
 *      Array of fields to register.
 *
 * 		@type string $id          A unique id used for the field. This id will also be used to save the value in
 * 		      					  the database.
 * 		@type string $type 		  The type of field to use. Please refer to the Beans core field types for more
 * 		      					  information. Custom field types are accepted here.
 *      @type string $label 	  The field label. Default false.
 *      @type string $description The field description. The description can be truncated using <!--more-->
 *            					  as a delimiter. Default false.
 *      @type array  $attributes  An array of attributes to add to the field. The array key defines the
 *            					  attribute name and the array value defines the attribute value. Default array.
 *      @type mixed  $default     The default field value. Default false.
 *      @type array  $fields      Must only be used for 'group' field type. The array arguments are similar to the
 *            					  {@see beans_register_fields()} $fields arguments.
 *      @type bool   $db_group    Must only be used for 'group' field type. It defines whether the group of fields
 *            					  registered should be saved as a group in the database or as individual
 *            					  entries. Default false.
 * }
 * @param string $context The context in which the fields are used. 'option' for options/settings pages, 'post_meta'
 *                        for post fields, 'term_meta' for taxonomies fields and 'wp_customize' for WP customizer
 *                        fields.
 * @param string $section A section id to define the group of fields.
 *
 * @return bool True on success, false on failure.
 */
function beans_register_fields( array $fields, $context, $section ) {

	if ( empty( $fields ) ) {
		return false;
	}

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once( BEANS_API_PATH . 'fields/class.php' );

	$class = new _Beans_Fields();
	$class->register( $fields, $context, $section );

	return true;

}

/**
 * Get registered fields.
 *
 * This function is used to get the previously registered fields in order to display them using
 * {@see beans_field()}.
 *
 * @since 1.0.0
 *
 * @param string $context The context in which the fields are used. 'option' for options/settings pages, 'post_meta'
 *                        for post fields, 'term_meta' for taxonomies fields and 'wp_customize' for WP customizer
 *                        fields.
 * @param string $section Optional. A section id to define a group of fields. This is mostly used for metaboxes
 *                        and WP Customizer sections.
 *
 * @return array|bool Array of register fields on success, false on failure.
 */
function beans_get_fields( $context, $section = false ) {

	if ( ! class_exists( '_Beans_Fields' ) ) {
		return;
	}

	$class = new _Beans_Fields();
	return $class->get_fields( $context, $section );

}

/**
 * Echo a field.
 *
 * This function echos the field content. Must be used in the loop of fields obtained using
 * {@see beans_get_fields()}.
 *
 * @since 1.0.0
 *
 * @param array $field Array of data obtained using {@see beans_get_fields()}.
 */
function beans_field( $field ) {

	if ( ! class_exists( '_Beans_Fields' ) ) {
		return;
	}

	$class = new _Beans_Fields();
	$class->field_content( $field );

}

/**
 * Standardize fields.
 *
 * @ignore
 */
function _beans_pre_standardize_fields( $fields ) {

	$_fields = array();

	foreach ( $fields as $field ) {

		$_fields[ $field['id'] ] = $field;

		if ( 'group' === beans_get( 'type', $field ) ) {
			$_fields[ $field['id'] ]['fields'] = _beans_pre_standardize_fields( $field['fields'] );
		}
	}

	return $_fields;

}
