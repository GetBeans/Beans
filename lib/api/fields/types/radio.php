<?php
/**
 * Renders the radio field type.
 *
 * @package Beans\Framework\API\Fields\Types
 */

beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );
/**
 * Echo radio field type.
 *
 * @since 1.0.0
 *
 * @param array $field      {
 *                          For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type mixed  $value      The field value.
 * @type string $name       The field name value.
 * @type array  $attributes An array of attributes to add to the field. The array key defines the attribute name and
 *       the array value defines the attribute value. Default array.
 * @type mixed  $default    The default value. Default false.
 * @type array  $options    An array used to populate the radio options. The array key defines radio value and the
 *       array value defines the radio label or image path.
 * }
 */
function beans_field_radio( array $field ) {

	if ( empty( $field['options'] ) ) {
		return;
	}

	$field['default'] = key( $field['options'] );

	include dirname( __FILE__ ) . '/views/radio.php';
}

/**
 * Checks if the radio is an image.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param string $radio The given radio to check.
 *
 * @return bool
 */
function _beans_is_radio_image( $radio ) {

	return in_array(
		beans_get( 'extension', pathinfo( $radio ) ),
		array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'ico' ),
		true
	);
}
