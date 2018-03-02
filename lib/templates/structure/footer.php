<?php
/**
 * Despite its name, this template echos between the closing primary markup and the closing HTML markup.
 *
 * This template must be called using get_footer().
 *
 * @package Structure\Footer
 */

						beans_close_markup_e( 'beans_primary', 'div' );

					beans_close_markup_e( 'beans_main_grid', 'div' );

				beans_close_markup_e( 'beans_fixed_wrap[_main]', 'div' );

			beans_close_markup_e( 'beans_main', 'main' );

		beans_close_markup_e( 'beans_site', 'div' );

		wp_footer();

	beans_close_markup_e( 'beans_body', 'body' );

beans_close_markup_e( 'beans_html', 'html' );
