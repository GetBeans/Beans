<?php
/**
 * This file contains Beans accessibility features.
 *
 * @package Beans\Framework\API\HTML
 *
 * @since   1.5.0
 */

/**
 * Build the skip links.
 *
 * @since 1.5.0
 *
 * @return null|bool Returns false when no skip links, otherwise null.
 */
function beans_build_skip_links() {
	$skip_links = array();
	$layout     = beans_get_layout();

	if ( has_nav_menu( 'primary' ) ) {
		$skip_links['beans-primary-navigation'] = __( 'Skip to the primary navigation.', 'tm-beans' );
	}

	$skip_links['beans-content'] = __( 'Skip to the content.', 'tm-beans' );

	if ( 'c' !== $layout ) {

		if ( beans_has_primary_sidebar( $layout ) ) {
			$skip_links['beans-primary-sidebar'] = __( 'Skip to the primary sidebar.', 'tm-beans' );
		}

		if ( beans_has_secondary_sidebar( $layout ) ) {
			$skip_links['beans-secondary-sidebar'] = __( 'Skip to the secondary sidebar.', 'tm-beans' );
		}
	}

	/**
	 * Filter the skip links.
	 *
	 * @since 1.5.0
	 *
	 * @param array $skip_links {
	 *     Default skip links.
	 *
	 *     @type string HTML ID attribute value to link to.
	 *     @type string Anchor text.
	 * }
	 */
	$skip_links = (array) apply_filters( 'beans_skip_links_list', $skip_links );

	if ( empty( $skip_links ) ) {
		return false;
	}

	beans_output_skip_links( $skip_links );
}

/**
 * Skip links output.
 *
 * @since 1.5.0
 *
 * @param array $skip_links Array of skip links to render.
 *
 * @return void
 */
function beans_output_skip_links( array $skip_links ) {
	beans_open_markup_e( 'beans_skip_links_list', 'ul', array( 'class' => 'beans-skip-links' ) );

	foreach ( $skip_links as $link => $link_name ) {
		beans_open_markup_e( 'beans_skip_links_item', 'li' );

			beans_open_markup_e(
				'beans_skip_links_item_link',
				'a',
				array(
					'href'  => '#' . $link,
					'class' => 'screen-reader-shortcut',
				)
			);

				echo esc_html( $link_name );

			beans_close_markup_e( 'beans_skip_links_item', 'a' );

		beans_close_markup_e( 'beans_skip_links_item', 'li' );
	}
	beans_close_markup_e( 'beans_skiplinks', 'ul' );
}


beans_add_smart_action( 'beans_accessibility_enqueue_skip_link_fix', 'beans_accessibility_skip_link_fix' );
/**
 * Enqueue the JavaScript fix for Internet Explorer 11
 *
 * @since 1.5.0
 *
 * return void
 */
function beans_accessibility_skip_link_fix() {
	$js = BEANS_API_URL . 'html/assets/js/skip-link-fix' . BEANS_MIN_JS . '.js';
	wp_enqueue_script( 'beans-skip-link-fix', $js, array(), BEANS_VERSION );
}
