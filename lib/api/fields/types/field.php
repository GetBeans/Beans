<?php
/**
 * @package API\Fields
 */

beans_add_smart_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
/**
 * Echo field label.
 *
 * @since 1.0.0
 *
 * @param array $field {
 *      Array of data.
 *
 *      @type string $label The field label. Default false.
 * }
 */
function beans_field_label( $field ) {

	if ( ! $label = beans_get( 'label', $field ) ) {
		return;
	}

	beans_open_markup_e( 'beans_field_label[_' . $field['id'] . ']', 'label' );

		echo $field['label'];

	beans_close_markup_e( 'beans_field_label[_' . $field['id'] . ']', 'label' );

}

beans_add_smart_action( 'beans_field_wrap_append_markup', 'beans_field_description' );
/**
 * Echo field description.
 *
 * @since 1.0.0
 *
 * @param array $field {
 *      Array of data.
 *
 *      @type string $description The field description. The description can be truncated using <!--more-->
 *            					  as a delimiter. Default false.
 * }
 */
function beans_field_description( $field ) {

	if ( ! $description = beans_get( 'description', $field ) ) {
		return;
	}

	beans_open_markup_e( 'beans_field_description[_' . $field['id'] . ']', 'div', array( 'class' => 'bs-field-description' ) );

		if ( preg_match( '#<!--more-->#', $description, $matches ) ) {
			list( $description, $extended ) = explode( $matches[0], $description, 2 );
		}

		echo $description;

		if ( isset( $extended ) ) {

			?>
			<br /><a class="bs-read-more" href="#"><?php _e( 'More...', 'tm-beans' ); ?></a>
			<div class="bs-extended-content"><?php echo $extended; ?></div>
			<?php

		}

	beans_close_markup_e( 'beans_field_description[_' . $field['id'] . ']', 'div' );

}
