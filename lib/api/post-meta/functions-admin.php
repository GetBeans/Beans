<?php
/**
 * The Beans Post Meta component extends the Beans Fields and makes it easy to add fields to any Post Type.
 *
 * @package Beans\Framework\API\Post_Meta
 *
 * @since 1.0.0
 */

/**
 * Register Post Meta.
 *
 * This function should only be invoked through the 'admin_init' action.
 *
 * @since 1.0.0
 *
 * @param array        $fields {
 *            Array of fields to register.
 *
 * @type string $id A unique ID used for the field. This ID will also be used to save the value in
 *                                the database.
 * @type string $type The type of field to use. Please refer to the Beans core field types for more
 *                                information. Custom field types are accepted here.
 * @type string $label The field label. Default false.
 * @type string $description The field description. The description can be truncated using <!--more-->
 *                                as a delimiter. Default false.
 * @type array $attributes An array of attributes to add to the field. The array key defines the
 *                                attribute name and the array value defines the attribute value. Default array.
 * @type mixed $default The default field value. Default false.
 * @type array $fields Must only be used for the 'group' field type. The array arguments are similar to the
 *                                {@see beans_register_fields()} $fields arguments.
 * @type bool $db_group Must only be used for the 'group' field type. Defines whether the group of fields
 *                                registered should be saved as a group in the database or as individual
 *                                entries. Default false.
 * }
 *
 * @param string|array $conditions Array of 'post type ID(s)', 'post ID(s)' or 'page template slug(s)' for which the post meta should be registered.
 *                                 'page template slug(s)' must include the '.php' file extention. Set to true to display everywhere.
 * @param string       $section A section ID to define the group of fields.
 * @param array        $args {
 *            Optional. Array of arguments used to register the fields.
 *
 * @type string $title The metabox Title. Default 'Undefined'.
 * @type string $context Where on the page the metabox should be shown
 *                             ('normal', 'advanced', or 'side'). Default 'normal'.
 * @type int $priority The priority within the context where the boxes should show
 *                             ('high', 'core', 'default' or 'low'). Default 'high'.
 * }
 *
 * @return bool True on success, false on failure.
 */
function beans_register_post_meta( array $fields, $conditions, $section, $args = array() ) {
	global $_beans_post_meta_conditions;

	if ( empty( $fields ) ) {
		return false;
	}

	/**
	 * Filter the post meta fields.
	 *
	 * The dynamic portion of the hook name, $section, refers to the section ID which defines the group of fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields An array of post meta fields.
	 */
	$fields = apply_filters( "beans_post_meta_fields_{$section}", _beans_pre_standardize_fields( $fields ) );

	/**
	 * Filter the conditions used to define whether the fields set should be displayed or not.
	 *
	 * The dynamic portion of the hook name, $section, refers to the section ID which defines the group of fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $conditions Conditions used to define whether the fields set should be displayed or not.
	 */
	$conditions = apply_filters( "beans_post_meta_post_types_{$section}", $conditions );

	$_beans_post_meta_conditions = array_merge( $_beans_post_meta_conditions, (array) $conditions );

	// Stop here if the current page isn't concerned.
	if ( ! _beans_is_post_meta_conditions( $conditions ) || ! is_admin() ) {
		return false;
	}

	// Stop here if the field can't be registered.
	if ( ! beans_register_fields( $fields, 'post_meta', $section ) ) {
		return false;
	}

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once BEANS_API_PATH . 'post-meta/class-beans-post-meta.php';

	new _Beans_Post_Meta( $section, $args );

	return true;
}

/**
 * Check the current screen conditions.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @param array|bool $conditions Conditions to show a Post Meta box.
 *
 * @return bool
 */
function _beans_is_post_meta_conditions( $conditions ) {

	// If user has designated boolean true, it's always true. Nothing more to do here.
	if ( true === $conditions ) {
		return true;
	}

	// Check if it is a new post and treat it as such.
	if ( false !== stripos( $_SERVER['REQUEST_URI'], 'post-new.php' ) ) {
		$current_post_type = beans_get( 'post_type' );

		if ( ! $current_post_type ) {

			if ( in_array( 'post', (array) $conditions, true ) ) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		// Try to get ID from $_GET.
		$id_get = beans_get( 'post' );
		// Try to get ID from $_POST.
		$id_post = beans_post( 'post_ID' );

		if ( $id_get ) {
			$post_id = $id_get;
		} elseif ( $id_post ) {
			$post_id = $id_post;
		}

		if ( ! isset( $post_id ) ) {
			return false;
		}

		$current_post_type = get_post_type( $post_id );
	}

	$statements = array(
		in_array( $current_post_type, (array) $conditions, true ), // Check post type.
		isset( $post_id ) && in_array( $post_id, (array) $conditions, true ), // Check post ID.
		isset( $post_id ) && in_array( get_post_meta( $post_id, '_wp_page_template', true ), (array) $conditions, true ), // Check page template.
	);

	// Return true if any condition is met, otherwise false.
	return in_array( true, $statements, true );
}

add_action( 'admin_print_footer_scripts', '_beans_post_meta_page_template_reload' );
/**
 * Reload post edit screen on page template change.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @return void
 */
function _beans_post_meta_page_template_reload() {
	global $_beans_post_meta_conditions, $pagenow;

	// Stop here if not editing a post object.
	if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ), true ) ) {
		return;
	}

	// Stop here if there is no post meta assigned to page templates.
	if ( false === stripos( wp_json_encode( $_beans_post_meta_conditions ), '.php' ) ) {
		return;
	}

	include dirname( __FILE__ ) . '/views/script.php';
}

/**
 * Initialize post meta conditions.
 *
 * @ignore
 * @access private
 */
global $_beans_post_meta_conditions;

if ( ! isset( $_beans_post_meta_conditions ) ) {
	$_beans_post_meta_conditions = array();
}
