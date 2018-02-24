<?php
/**
 * View file for the image field type.
 *
 * @package Beans\Framework\API\Fields\Types
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>

<button class="bs-add-image button button-small" type="button" <?php echo isset( $hide_add_link ) ? 'style="display: none"' : ''; ?>><?php echo esc_html( $link_text ); ?></button>
<input id="<?php echo esc_attr( $field['id'] ); ?>" type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="">
<div class="bs-images-wrap" data-multiple="<?php echo esc_attr( $is_multiple ); ?>">
<?php

foreach ( $images as $id ) :

	// Skip this one if the ID is not set.
	if ( ! $id ) {
		continue;
	}

	$attributes = _beans_get_image_id_attributes( $id, $field, $is_multiple ); // @codingStandardsIgnoreLine. WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound - Called from within a function and not within global scope.
	list( $image_url, $image_alt ) = _beans_get_image_url_and_alt( $id );
?>
	<div class="bs-image-wrap<?php echo 'placeholder' === $id ? ' bs-image-template' : ''; ?>">
        <input <?php echo beans_esc_attributes( $attributes ); ?> /><?php // @codingStandardsIgnoreLine. WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaping is handled in the function. ?>
		<img src="<?php echo $image_url ? esc_url( $image_url ) : ''; ?>" alt="<?php echo $image_alt ? esc_attr( $image_alt ) : ''; ?>">
		<div class="bs-toolbar">
		<?php if ( $is_multiple ) : ?>
			<button aria-label="<?php esc_attr_e( 'Manage Images', 'tm-beans' ); ?>" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
		<?php endif; ?>
			<button aria-label="<?php esc_attr_e( 'Edit Image', 'tm-beans' ); ?>" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
			<button aria-label="<?php esc_attr_e( 'Delete Image', 'tm-beans' ); ?>" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
		</div>
	</div>
<?php endforeach; ?>
</div>
