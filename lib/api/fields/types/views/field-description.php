<?php
/**
 * View file for the field's description.
 *
 * @package Beans\Framework\API\Fields\Types
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>

<br /><a class="bs-read-more" href="#"><?php esc_html_e( 'More...', 'tm-beans' ); ?></a>
<div class="bs-extended-content" style="display: none;"><?php echo $extended; ?></div><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- To optimize, escaping is handled in the calling function. ?>
