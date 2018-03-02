<?php
/**
 * Since WordPress force us to use the header.php name to open the document, we add a header-partial.php template for the actual header.
 *
 * @package Structure\Header
 */

beans_open_markup_e( 'beans_header', 'header', array(
	'class'     => 'tm-header uk-block',
	'role'      => 'banner',
	'itemscope' => 'itemscope',
	'itemtype'  => 'http://schema.org/WPHeader',
) );

	beans_open_markup_e( 'beans_fixed_wrap[_header]', 'div', 'class=uk-container uk-container-center' );

		/**
		 * Fires in the header.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_header' );

	beans_close_markup_e( 'beans_fixed_wrap[_header]', 'div' );

beans_close_markup_e( 'beans_header', 'header' );
