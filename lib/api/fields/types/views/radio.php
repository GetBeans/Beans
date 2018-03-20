<?php
/**
 * View file for the radio field type.
 *
 * @package Beans\Framework\API\Fields\Types
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variables are used within a function's scope.
?>

<fieldset>
	<legend class=""><?php echo esc_html( $field['label'] ); ?></legend>
<?php

// Clean the field's ID prefix once before we start the loop.
$id_prefix = esc_attr( $field['id'] . '_' );

foreach ( $field['options'] as $value => $radio ) :
	$is_image = _beans_is_radio_image( $radio );

	// Clean the value here to avoid calling esc_attr() again and again for the same value.
	$clean_value = esc_attr( $value );
	$clean_id    = $id_prefix . $clean_value;
?>
		<label class="<?php echo $is_image ? 'bs-has-image' : ''; ?>" for="<?php echo $clean_id; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Escaped above. ?>">
		<?php
		if ( $is_image ) :
			$image = _beans_standardize_radio_image( $value, $radio );
		?>
			<span class="screen-reader-text"><?php echo esc_html( $image['screen_reader_text'] ); ?></span>
			<img src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_html( $image['alt'] ); ?>" />
			<input id="<?php echo $clean_id; ?>" class="screen-reader-text" type="radio" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo $clean_value; ?>"<?php checked( $value, $field['value'], 1 ); ?><?php echo beans_esc_attributes( $field['attributes'] ); ?> /> <?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- The variable is escaped above. beans_esc_attributes is escaped by the function. ?>
		<?php endif; ?>

<?php if ( ! $is_image ) : ?>
	<input id="<?php echo $clean_id; ?>" type="radio" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo $clean_value; ?>"<?php checked( $value, $field['value'], 1 ); ?><?php echo beans_esc_attributes( $field['attributes'] ); ?> /> <?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- The variable is escaped above. beans_esc_attributes is escaped by the function. ?>
	<?php
	echo wp_kses_post( $radio );
endif;
?>
		</label>
<?php
endforeach;
?>
</fieldset>
<?php
// phpcs:enable
