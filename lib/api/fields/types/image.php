<?php
/**
 * Handler for rendering the image field.
 *
 * @package Beans\Framework\API\Fields\Types
 */

beans_add_smart_action( 'beans_field_enqueue_scripts_image', 'beans_field_image_assets' );
/**
 * Enqueued the assets for the image field.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_field_image_assets() {
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'beans-field-media', BEANS_API_URL . 'fields/assets/js/media' . BEANS_MIN_CSS . '.js', array( 'jquery' ), BEANS_VERSION );
}

beans_add_smart_action( 'beans_field_image', 'beans_field_image' );
/**
 * Render the image field, which handles a single ime or a gallery of images.
 *
 * @since 1.0.0
 * @since 1.5.0 Moved the HTML to a view file.
 *
 * @param array $field       {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type mixed  $value       The image's or images' ID.
 * @type string $name        The field's "name" value.
 * @type array  $attributes  An array of attributes to add to the field. The array's key defines the attribute name
 *                           and the array's value defines the attribute value. Default is an empty array.
 * @type mixed  $default     The default value. Default is false.
 * @type string $is_multiple Set to true to enable multiple images (gallery). Default is false.
 * }
 */
function beans_field_image( array $field ) {
	$images      = array_merge( (array) $field['value'], array( 'placeholder' ) );
	$is_multiple = beans_get( 'multiple', $field );
	$link_text   = _n( 'Add Image', 'Add Images', ( $is_multiple ? 2 : 1 ), 'tm-beans' );

	// If this is a single image and it already exists, then hide the "add image" hyperlink.
	$hide_add_link = ! $is_multiple && is_numeric( $field['value'] );

	// Render the view file.
	include dirname( __FILE__ ) . '/views/image.php';
}

/**
 * Get the Image ID's attributes.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param string $id          The given image's ID.
 * @param array  $field       The field's configuration parameters.
 * @param bool   $is_multiple Multiple flag.
 *
 * @return array
 */
function _beans_get_image_id_attributes( $id, array $field, $is_multiple ) {
	$attributes = array_merge( array(
		'class' => 'image-id',
		'type'  => 'hidden',
		'name'  => $is_multiple ? $field['name'] . '[]' : $field['name'], // Return single value if not multiple.
		'value' => $id,
	), $field['attributes'] );

	if ( 'placeholder' === $id ) {
		$attributes = array_merge(
			$attributes,
			array(
				'disabled' => 'disabled',
				'value'    => false,
			)
		);
	}

	return $attributes;
}
