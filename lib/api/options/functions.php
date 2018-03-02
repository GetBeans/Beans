<?php
/**
 * The Beans Options component extends the Beans Fields and make it easy to add fields to a WordPress options page.
 *
 * @package API\Options
 */

/**
 * Register options.
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
 *      @type bool   $db_group    Must only be used for 'group' field types. Defines whether the group of fields
 *            					  registered should be saved as a group in the database or as individual
 *            					  entries. Default false.
 * }
 * @param string $menu_slug The menu slug used by fields.
 * @param string $section   A section id to define the group of fields.
 * @param array  $args {
 *      Optional. Array of arguments used to register the fields.
 *
 * 		@type string $title   The metabox Title. Default 'Undefined'.
 * 		@type string $context Where on the page where the metabox should be shown
 * 		      				  ('normal', 'column'). Default 'normal'.
 * }
 *
 * @return bool True on success, false on failure.
 */
function beans_register_options( array $fields, $menu_slug, $section, $args = array() ) {

	/**
	 * Filter the options fields.
	 *
	 * The dynamic portion of the hook name, $section, refers to the section id which defines the group of fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields An array of options fields.
	 */
	$fields = apply_filters( "beans_options_fields_{$section}", _beans_pre_standardize_fields( $fields ) );

	/**
	 * Filter the options fields menu slug.
	 *
	 * The dynamic portion of the hook name, $section, refers to the section id which defines the group of fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $menu_slug The menu slug.
	 */
	$menu_slug = apply_filters( "beans_options_menu_slug_{$section}", $menu_slug );

	// Stop here if the page isn't concerned.
	if ( ( beans_get( 'page' ) !== $menu_slug ) || ! is_admin() ) {
		return;
	}

	// Stop here if the field can't be registered.
	if ( ! beans_register_fields( $fields, 'option', $section ) ) {
		return false;
	}

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once( BEANS_API_PATH . 'options/class.php' );

	$class = new _Beans_Options();
	$class->register( $section, $args );

	return true;

}

/**
 * Echo the registered options.
 *
 * This function echos the options registered for the defined admin page.
 *
 * @since 1.0.0
 *
 * @param array $menu_slug The menu slug used to register the options.
 */
function beans_options( $menu_slug ) {

	if ( ! class_exists( '_Beans_Options' ) ) {
		return false;
	}

	$class = new _Beans_Options();
	$class->page( $menu_slug );

}

add_action( 'wp_loaded', '_beans_options_page_actions' );
/**
 * Fires the options form actions.
 *
 * @ignore
 */
function _beans_options_page_actions() {

	if ( ! beans_post( 'beans_options_nonce' ) ) {
		return;
	}

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once( BEANS_API_PATH . 'options/class.php' );

	$class = new _Beans_Options();
	$class->actions();

}
