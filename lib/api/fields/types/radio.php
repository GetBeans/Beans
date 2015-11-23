<?php
/**
 * @package API\Fields\Types
 */

beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );

/**
 * Echo radio field type.
 *
 * @since 1.0.0
 *
 * @param array $field {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 *      @type mixed  $value      The field value.
 *      @type string $name       The field name value.
 *      @type array  $attributes An array of attributes to add to the field. The array key defines the
 *            					 attribute name and the array value defines the attribute value. Default array.
 *      @type mixed  $default    The default value. Default false.
 *      @type array  $options    An array used to populate the radio options. The array key defines radio
 *            					 value and the array value defines the radio label or image path.
 * }
 */
function beans_field_radio( $field ) {

	if ( empty( $field['options'] ) )
		return;

	$field['default'] = isset( $checkbox['default'] ) ? $checkbox['default'] : key( $field['options'] );

	echo '<fieldset>';

		$i = 0; foreach ( $field['options'] as $id => $radio ) {

			$checked = $id == $field['value'] ? ' checked="checked"' : null;

			$has_image = @getimagesize( BEANS_ADMIN_ASSETS_PATH . '/' . $radio ) ? 'bs-has-image' : false;

			echo '<label class="' . $has_image . '">';

				if ( $has_image )
					echo '<img src="' BEANS_ADMIN_ASSETS_URL . '/' . $radio . '" />';

				echo '<input type="radio" name="' . $field['name'] . '" value="' . $id . '" ' . $checked . ' ' . beans_sanatize_attributes( $field['attributes'] ) . '/>';

				if ( !$has_image )
					echo $radio;

			echo '</label>';

			$i++;

		}

	echo '</fieldset>';

}