<?php
/**
 * View file for the radio field type.
 *
 * @package Beans\Framework\API\Fields\Types
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>

<fieldset>
<?php

foreach ( $field['options'] as $value => $radio ) : // @codingStandardsIgnoreLine. WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound - Called from within a function and not within global scope.
    $is_image = _beans_is_radio_image( $radio ); // @codingStandardsIgnoreLine. WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound - Called from within a function and not within global scope.

	// Clean the value here to avoid calling esc_attr() again and again for the same value.
	$clean_value = esc_attr( $value ); // @codingStandardsIgnoreLine. WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound - Called from within a function and not within global scope.
?>
		<label class="<?php echo $is_image ? 'bs-has-image' : ''; ?>" for="radio_<?php echo $clean_value; // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaped above. ?>">
		<?php
		if ( $is_image ) :
			$image = _beans_standardize_radio_image( $value, $radio );  // @codingStandardsIgnoreLine. WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound - Called from within a function and not within global scope.
		?>
			<span class="screen-reader-text"><?php echo esc_html( $image['screen_reader_text'] ); ?></span>
			<img src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_html( $image['alt'] ); ?>" />
		<?php endif; ?>
			<input id="radio_<?php echo $clean_value; ?>" type="radio" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo $clean_value; ?>"<?php checked( $value, $field['value'], 1 ); echo beans_esc_attributes( $field['attributes'] ); ?> /> <?php // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - Escaped above. ?>
<?php
if ( ! $is_image ) {
	echo wp_kses_post( $radio );
}
?>
		</label>
<?php
endforeach;
?>
</fieldset>
