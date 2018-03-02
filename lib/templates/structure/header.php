<?php
/**
 * Despite its name, this template echos between the opening HTML markup and the opening primary markup.
 *
 * This template must be called using get_header().
 *
 * @package Structure\Header
 */

beans_output_e( 'beans_doctype', '<!DOCTYPE html>' );

beans_open_markup_e( 'beans_html', 'html', str_replace( ' ', '&', str_replace( '"', '', beans_render_function( 'language_attributes' ) ) ) );

	beans_open_markup_e( 'beans_head', 'head' );

		/**
		 * Fires in the head.
		 *
		 * This hook fires in the head HTML section, not in wp_header().
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_head' );

		wp_head();

	beans_close_markup_e( 'beans_head', 'head' );

	beans_open_markup_e( 'beans_body', 'body', array(
		'class'     => implode( ' ', get_body_class( 'uk-form no-js' ) ),
		'itemscope' => 'itemscope',
		'itemtype'  => 'http://schema.org/WebPage',

	) );

		beans_open_markup_e( 'beans_site', 'div', array( 'class' => 'tm-site' ) );

			beans_open_markup_e( 'beans_main', 'main', array( 'class' => 'tm-main uk-block' ) );

				beans_open_markup_e( 'beans_fixed_wrap[_main]', 'div', 'class=uk-container uk-container-center' );

					beans_open_markup_e( 'beans_main_grid', 'div', array( 'class' => 'uk-grid', 'data-uk-grid-margin' => '' ) );

						beans_open_markup_e( 'beans_primary', 'div', array(
							'class' => 'tm-primary ' . beans_get_layout_class( 'content' ),
						) );
