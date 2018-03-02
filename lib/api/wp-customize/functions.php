<?php
/**
 * The Beans WP Customize component extends the Beans Fields and make it easy add options to the
 * WP Theme Customizer.
 *
 * @package API\WP_Customize
 */

/**
 * Register WP Customize Options.
 *
 * This function should only be invoked through the 'customize_register' action.
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
 *      @type array  $fields      Must only be used for 'group' field type. The array arguments are similary to the
 *            					  {@see beans_register_fields()} $fields arguments.
 *      @type bool   $db_group    Must only be used for 'group' field type. It defines whether the group of fields
 *            					  registered should be saved as a group in the database or as individual
 *            					  entries. Default false.
 * }
 * @param string $section    The WP customize section to which the fields should be added. Add a unique id
 *                           to create a new section.
 * @param array  $args {
 *      Optional. Array of arguments used to register the fields.
 *
 * 		@type string $title       The visible name of a controller section.
 * 		@type int    $priority    This controls the order in which this section appears
 * 		      					  in the Theme Customizer sidebar. Default 30.
 * 		@type string $description This optional argument can add additional descriptive
 * 		      					  text to the section. Default false.
 * }
 *
 * @return bool True on success, false on failure.
 */
function beans_register_wp_customize_options( array $fields, $section, $args = array() ) {

	/**
	 * Filter the customizer fields.
	 *
	 * The dynamic portion of the hook name, $section, refers to the section id which defines the group of fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields An array of customizer fields.
	 */
	$fields = apply_filters( "beans_wp_customize_fields_{$section}", _beans_pre_standardize_fields( $fields ) );

	// Stop here if the current page isn't concerned.
	if ( ! is_customize_preview() ) {
		return;
	}

	// Stop here if the field can't be registered.
	if ( ! beans_register_fields( $fields, 'wp_customize', $section ) ) {
		return false;
	}

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once( BEANS_API_PATH . 'wp-customize/class.php' );

	new _Beans_WP_Customize( $section, $args );

}
