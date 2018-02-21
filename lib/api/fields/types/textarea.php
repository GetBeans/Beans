<?php
/**
 * Render the textarea field type.
 *
 * @package Beans\Framework\API\Options
 */

beans_add_smart_action( 'beans_field_textarea', 'beans_field_textarea' );
/**
 * Render the textarea field type.
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
function beans_field_textarea( $field ) {
	printf( '<textarea name="%s" %s>%s</textarea>',
		esc_attr( $field['name'] ),
		beans_esc_attributes( $field['attributes'] ), // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaping is handled in the function.
		esc_textarea( $field['value'] )
	);
}
