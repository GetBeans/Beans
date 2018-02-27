<?php
/**
 * Extends WordPress Embed.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

// Filter.
beans_add_smart_action( 'embed_oembed_html', 'beans_embed_oembed' );
/**
 * Add markup to embed.
 *
 * @since 1.0.0
 *
 * @param string $html The embed HTML.
 *
 * @return string The modified embed HTML.
 */
function beans_embed_oembed( $html ) {
	$output = beans_open_markup( 'beans_embed_oembed', 'div', 'class=tm-oembed' );

		$output .= $html;

	$output .= beans_close_markup( 'beans_embed_oembed', 'div' );

	return $output;
}
