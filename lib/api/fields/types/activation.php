<?php
/**
 * Renders the activation field type.
 *
 * @package Beans\Framework\API\Options
 */

beans_add_smart_action( 'beans_field_activation', 'beans_field_activation' );
/**
 * Echo activation field type.
 *
 * @since 1.0.0
 *
 * @param array $field       {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type string $description The field description. The description can be truncated using <!--more--> as a delimiter.
 *       Default false.
 * @type mixed  $value       The field value.
 * @type string $name        The field name value.
 * @type array  $attributes  An array of attributes to add to the field. The array key defines the attribute name and
 *       the array value defines the attribute value. Default array.
 * @type mixed  $default     The default value. Default false.
 * }
 */
function beans_field_activation( array $field ) {
	include dirname( __FILE__ ) . '/views/activation.php';
}
