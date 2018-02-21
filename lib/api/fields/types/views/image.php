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

<a href="#" class="bs-add-image button button-small" <?php echo isset( $hide_add_link ) ? 'style="display: none"' : ''; ?>><?php echo esc_html( $link_text ); ?></a>
<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="">
<div class="bs-images-wrap" data-multiple="<?php echo esc_attr( $is_multiple ); ?>">
<?php

foreach ( $images as $id ) :

	// Skip this one if the ID is not set.
	if ( ! $id ) {
		continue;
	}

	$attributes = _beans_get_image_id_attributes( $id, $field, $is_multiple ); // @codingStandardsIgnoreLine. WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound - Called from within a function and not within global scope.
?>
	<div class="bs-image-wrap<?php echo 'placeholder' === $id ? ' bs-image-template' : ''; ?>">
        <input <?php echo beans_esc_attributes( $attributes ); ?> /><?php // @codingStandardsIgnoreLine phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaping is handled in the function. ?>
		<img src="<?php echo esc_url( beans_get( 0, wp_get_attachment_image_src( $id, 'thumbnail' ) ) ); ?>">
		<div class="bs-toolbar">
		<?php if ( $is_multiple ) : ?>
			<a href="#" class="dashicons dashicons-menu"></a>
		<?php endif; ?>
			<a href="#" class="dashicons dashicons-edit"></a>
			<a href="#" class="dashicons dashicons-post-trash"></a>
		</div>
	</div>
<?php endforeach; ?>
</div>
