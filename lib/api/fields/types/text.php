<?php
/**
 * Renders the slider field type.
 *
 * @package Beans\Framework\API\Options
 */

beans_add_smart_action( 'beans_field_text', 'beans_field_text' );
/**
 * Echo text field type.
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
 * }
 */
function beans_field_text( $field ) {
	printf( '<input type="text" name="%s" value="%s" %s>',
		esc_attr( $field['name'] ),
		esc_attr( $field['value'] ),
		beans_esc_attributes( $field['attributes'] ) // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaping is handled in the function.
	);
}
