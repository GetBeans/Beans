<?php
/**
 * View file for the activation field type.
 *
 * @package Beans\Framework\API\Fields\Types
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>

<input type="hidden" value="0" name="<?php echo esc_attr( $field['name'] ); ?>" />
<input id="<?php echo esc_html( $field['id'] ); ?>" type="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" value="1"<?php checked( $field['value'], 1 ); ?> <?php echo beans_esc_attributes( $field['attributes'] ); ?>/><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Escaping is handled in the function. ?>
<label for="<?php echo esc_html( $field['id'] ); ?>"><?php echo esc_attr( $field['label'] ); ?></label>
