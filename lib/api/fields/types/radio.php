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

	if ( empty( $field['options'] ) ) {
		return;
	}

	$field['default'] = isset( $checkbox['default'] ) ? $checkbox['default'] : key( $field['options'] );

	?>
	<fieldset>
		<?php $i = 0; foreach ( $field['options'] as $id => $radio ) :

			$extensions = array( 'jpg', 'jpeg', 'jpe',  'gif',  'png',  'bmp',   'tif',  'tiff', 'ico' );
			$has_image = in_array( beans_get( 'extension', pathinfo( $radio ) ), $extensions ) ? 'bs-has-image' : false;

			?>
			<label class="<?php echo esc_attr( $has_image ); ?>">
				<?php if ( $has_image ) : ?>
					<img src="<?php echo esc_url( $radio ); ?>"/>
				<?php endif; ?>
				<input type="radio" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $field['value'], 1 ); ?> <?php echo beans_esc_attributes( $field['attributes'] ); ?>/>
				<?php if ( ! $has_image ) : ?>
					<?php echo $radio; ?>
				<?php endif; ?>
			</label>

		<?php $i++; endforeach; ?>
	</fieldset>
	<?php

}
