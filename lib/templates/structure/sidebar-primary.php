<?php
/**
 * Echo the primary sidebar structural markup. It also calls the primary sidebar action hooks.
 *
 * @package Structure\Primary_Sidebar
 */

beans_open_markup_e( 'beans_sidebar_primary', 'aside', array(
	'class'     => 'tm-secondary ' . beans_get_layout_class( 'sidebar_primary' ), // Automatically escaped.
	'role'      => 'complementary',
	'itemscope' => 'itemscope',
	'itemtype'  => 'http://schema.org/WPSideBar',
) );

	/**
	 * Fires in the primary sidebar.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_sidebar_primary' );

beans_close_markup_e( 'beans_sidebar_primary', 'aside' );
