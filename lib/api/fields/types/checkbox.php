<?php
/**
 * Handler for rendering the checkbox field.
 *
 * @package Beans\Framework\API\Fields\Types
 */

beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
/**
 * Echo checkbox field.
 *
 * @since 1.0.0
 * @since 1.5.0 Moved the HTML to a view file.
 *
 * @param array $field          {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type mixed  $value          The field's current value.
 * @type string $name           The field's "name" value.
 * @type array  $attributes     An array of attributes to add to the field. The array's key defines the attribute name
 *                              and the array's value defines the attribute value. Default is an empty array.
 * @type mixed  $default        The default value. Default false.
 * @type string $checkbox_label The field checkbox label. Default is 'Enable'.
 * }
 */
function beans_field_checkbox( array $field ) {
	$checkbox_label = beans_get( 'checkbox_label', $field, __( 'Enable', 'tm-beans' ) );

	include dirname( __FILE__ ) . '/views/checkbox.php';
}
