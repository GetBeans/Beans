<?php
/**
 * Renders the select field type.
 *
 * @package Beans\Framework\API\Fields\Types
 */

beans_add_smart_action( 'beans_field_select', 'beans_field_select' );
/**
 * Echo select field type.
 *
 * @since 1.0.0
 *
 * @param array $field      {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type mixed  $value      The field value.
 * @type string $name       The field name value.
 * @type array  $attributes An array of attributes to add to the field. The array key defines the attribute name and
 *                          the array value defines the attribute value. Default array.
 * @type mixed  $default    The default value. Default false.
 * @type array  $options    An array used to populate the select options. The array key defines option value and the
 *                          array value defines the option label.
 * }
 */
function beans_field_select( array $field ) {

	if ( empty( $field['options'] ) ) {
		return;
	}

	include dirname( __FILE__ ) . '/views/select.php';
}
