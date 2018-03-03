<?php
/**
 * Echo widget areas.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_sidebar_primary', 'beans_widget_area_sidebar_primary' );
/**
 * Echo primary sidebar widget area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_area_sidebar_primary() {
	echo beans_widget_area( 'sidebar_primary' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
}

beans_add_smart_action( 'beans_sidebar_secondary', 'beans_widget_area_sidebar_secondary' );
/**
 * Echo secondary sidebar widget area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_area_sidebar_secondary() {
	echo beans_widget_area( 'sidebar_secondary' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
}

beans_add_smart_action( 'beans_site_after_markup', 'beans_widget_area_offcanvas_menu' );
/**
 * Echo off-canvas widget area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_widget_area_offcanvas_menu() {

	if ( ! current_theme_supports( 'offcanvas-menu' ) ) {
		return;
	}

	echo beans_widget_area( 'offcanvas_menu' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
}
