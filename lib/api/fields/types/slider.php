<?php
/**
 * Renders the slider field type.
 *
 * @package Beans\Framework\API\Options
 */

beans_add_smart_action( 'beans_field_enqueue_scripts_slider', 'beans_field_slider_assets' );
/**
 * Enqueued assets required by the beans slider field.
 *
 * @since 1.0.0
 */
function beans_field_slider_assets() {
	wp_enqueue_script( 'jquery-ui-slider' );
}

beans_add_smart_action( 'beans_field_slider', 'beans_field_slider' );
/**
 * Echo slider field type.
 *
 * @since 1.0.0
 *
 * @param array    $field      {
 *                             For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 * @type mixed     $value      The field's current value.
 * @type string    $name       The field's "name" value.
 * @type array     $attributes An array of attributes to add to the field. The array key defines the
 *                                 attribute name and the array value defines the attribute value. Default array.
 * @type int|float $default    The default value.
 * @type string    $min        The slider's minimum value. Default 0.
 * @type string    $max        The slider's maximum value. Default 100.
 * @type string    $interval   The slider's interval. Default 1.
 * @type string    $unit       The slider's units, which is displayed after the current value. Default null.
 * }
 */
function beans_field_slider( $field ) {
	$defaults = array(
		'min'      => 0,
		'max'      => 100,
		'interval' => 1,
		'unit'     => null,
	);

	$field = array_merge( $defaults, $field );

	?>
	<div class="bs-slider-wrap" slider_min="<?php echo (int) $field['min']; ?>" slider_max="<?php echo (int) $field['max']; ?>" slider_interval="<?php echo (int) $field['interval']; ?>">

		<?php // Don't make this a hidden field to prevent triggering issues with wp_customise. ?>
		<input type="text" value="<?php echo esc_attr( $field['value'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" style="display: none;" <?php echo beans_esc_attributes( $field['attributes'] ); ?>/><?php // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaping is handled in the function. ?>

	</div>
	<span class="bs-slider-value"><?php echo esc_html( $field['value'] ); ?></span>

	<?php if ( $field['unit'] ) : ?>
		<span class="bs-slider-unit"><?php echo esc_html( $field['unit'] ); ?></span>
	<?php
	endif;
}
