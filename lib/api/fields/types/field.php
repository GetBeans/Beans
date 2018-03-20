<?php
/**
 * Handler for rendering the field's label and description.
 *
 * @package Beans\Framework\API\Fields\Types
 */

beans_add_smart_action( 'beans_field_group_label', 'beans_field_label' );
beans_add_smart_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
/**
 * Render the field's label.
 *
 * @since 1.0.0
 *
 * @param array $field {
 *                     Array of data.
 *
 * @type string $label The field label. Default false.
 * }
 */
function beans_field_label( array $field ) {

	// These field types do not use a label, as they are using fieldsets with legends.
	if ( in_array( $field['type'], array( 'radio', 'group', 'activation' ), true ) ) {
		return;
	}

	$label = beans_get( 'label', $field );

	if ( ! $label ) {
		return;
	}

	$id   = 'beans_field_label[_' . $field['id'] . ']';
	$tag  = 'label';
	$args = array( 'for' => $field['id'] );

	beans_open_markup_e( $id, $tag, $args );
		echo esc_html( $field['label'] );
	beans_close_markup_e( $id, $tag );
}

beans_add_smart_action( 'beans_field_wrap_append_markup', 'beans_field_description' );
/**
 * Render the field's description.
 *
 * @since 1.0.0
 * @since 1.5.0 Moved the HTML to a view file.
 *
 * @param array $field       {
 *                           Array of data.
 *
 * @type string $description The field description. The description can be truncated using <!--more--> as a delimiter.
 *                           Default false.
 * }
 */
function beans_field_description( array $field ) {
	$description = beans_get( 'description', $field );

	if ( ! $description ) {
		return;
	}
	// Escape the description here.
	$description = wp_kses_post( $description );

	// If the description has <!--more-->, split it.
	if ( preg_match( '#<!--more-->#', $description, $matches ) ) {
		list( $description, $extended ) = explode( $matches[0], $description, 2 );
	}

	beans_open_markup_e( 'beans_field_description[_' . $field['id'] . ']', 'div', array( 'class' => 'bs-field-description' ) );

		echo $description;  // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- To optimize, escaping is handled above.

	if ( isset( $extended ) ) {
		include dirname( __FILE__ ) . '/views/field-description.php';
	}

	beans_close_markup_e( 'beans_field_description[_' . $field['id'] . ']', 'div' );
}
