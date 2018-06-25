<?php
/**
 * Echo the secondary sidebar structural markup. It also calls the secondary sidebar action hooks.
 *
 * @package Beans\Framework\Templates\Structure
 *
 * @since   1.0.0
 * @since   1.5.0 Added ID and tabindex for skip links.
 */

beans_open_markup_e(
	'beans_sidebar_secondary',
	'aside',
	array(
		'class'     => 'tm-tertiary ' . beans_get_layout_class( 'sidebar_secondary' ), // Automatically escaped.
		'id'        => 'beans-secondary-sidebar',
		'role'      => 'complementary',
		'itemscope' => 'itemscope',
		'itemtype'  => 'https://schema.org/WPSideBar',
		'tabindex'  => '-1',
	)
);

	/**
	 * Fires in the secondary sidebar.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_sidebar_secondary' );

beans_close_markup_e( 'beans_sidebar_secondary', 'aside' );
