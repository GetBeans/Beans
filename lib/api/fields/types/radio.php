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
 * @param string|array $radio The given radio to check.
 *
 * @return bool
 */
function _beans_is_radio_image( $radio ) {

	if ( is_array( $radio ) ) {
		return true;
	}

	// Else, check the fallback.
	return in_array(
		beans_get( 'extension', pathinfo( $radio ) ),
		array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'ico' ),
		true
	);
}

/**
 * Standardize the radio image parameters.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param string       $value Value for the radio.
 * @param string|array $radio The given radio image.
 *
 * @return array
 */
function _beans_standardize_radio_image( $value, $radio ) {

	// Format when only the image's src is provided.
	if ( ! is_array( $radio ) ) {
		return array(
			'src'                => $radio,
			'alt'                => "Option for {$value}",
			'screen_reader_text' => "Option for {$value}",
		);
	}

	$radio = array_merge( array(
		'src'                => '',
		'alt'                => '',
		'screen_reader_text' => '',
	), $radio );

	if ( ! $radio['screen_reader_text'] && ! $radio['alt'] ) {
		$radio['alt']                = "Option for {$value}";
		$radio['screen_reader_text'] = "Option for {$value}";
	}

	if ( ! $radio['screen_reader_text'] && $radio['alt'] ) {
		$radio['screen_reader_text'] = $radio['alt'];
	}

	if ( ! $radio['alt'] && $radio['screen_reader_text'] ) {
		$radio['alt'] = $radio['screen_reader_text'];
	}

	return $radio;
}
