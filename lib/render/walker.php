<?php
/**
 * Extends WordPress walker.
 *
 * @ignore
 *
 * @package Render\Menu
 */

/**
 * Extends WordPress Walker_Nav_Menu class.
 *
 * @ignore
 */
class _Beans_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Extend WordPress start first menu level.
	 *
	 * @ignore
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {

		// Stop here if the depth is smaller than starting depth.
		if ( $depth < $args->beans_start_level ) {
			return;
		}

		$type = beans_get( 'beans_type', $args );
		$location_subfilter = ( $location = beans_get( 'theme_location', $args ) ) ? "[_{$location}]" : null;

		// Default attributes.
		$attr = array(
			'class' => array( 'sub-menu' ),
		);

		// Add UIKit sidenav and offcanvas class.
		if ( $depth > 0 || in_array( $type, array( 'sidenav', 'offcanvas' ) ) ) {
			$attr['class'][] = 'uk-nav-sub';
		}

		// Add UIKit navbar stuff.
		if ( 'navbar' === $type && $args->beans_start_level === $depth ) {

			// Add UIKit navbar attributes.
			$attr['class'][] = 'uk-nav uk-nav-parent-icon uk-nav-dropdown';
			$attr['data-uk-nav'] = '{multiple:true}';

			// Open sub_menu wrap.
			$output .= beans_open_markup( "beans_sub_menu_wrap[_{$type}]{$location_subfilter}", 'div', 'class=uk-dropdown uk-dropdown-navbar', $depth, $args );

		}

		// Implode to avoid empty spaces.
		$attr['class'] = implode( ' ', array_filter( $attr['class'] ) );

		// Set to null if empty to avoid outputing empty class html attribute.
		if ( ! $attr['class'] ) {
			$attr['class'] = null;
		}

		// Open sub_menu.
		$output .= beans_open_markup( "beans_sub_menu[_{$type}]{$location_subfilter}", 'ul', $attr, $depth, $args );

	}

	/**
	 * Extend WordPress end first menu level.
	 *
	 * @ignore
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {

		// Stop here if the depth is smaller than starting depth.
		if ( $depth < $args->beans_start_level ) {
			return;
		}

		$type = beans_get( 'beans_type', $args );
		$location_subfilter = ( $location = beans_get( 'theme_location', $args ) ) ? "[_{$location}]" : null;

		// Close sub_menu.
		$output .= beans_close_markup( "beans_sub_menu[_{$type}]{$location_subfilter}", 'ul' );

		// Close sub_menu wrap.
		if ( 'navbar' === $type && $args->beans_start_level === $depth ) {
			$output .= beans_close_markup( "beans_sub_menu_wrap[_{$type}]{$location_subfilter}", 'div', $depth, $args );
		}

	}

	/**
	 * Extend WordPress start menu elements.
	 *
	 * @ignore
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		// Stop here if the depth is smaller than starting depth.
		if ( $depth < $args->beans_start_level ) {
			return;
		}

		$item_id = $item->ID;

		// Wp item attributes.
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$_classes = join( ' ', (array) apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

		// WP link attributes.
		$_link_attr = array(
			'title'    => $item->attr_title,
			'target'   => $item->target,
			'rel'      => $item->xfn,
			'href'     => $item->url,
			'itemprop' => 'url',
		);

		// Prevent empty WP link attributes.
		foreach ( $_link_attr as $attr => $value ) {
			if ( empty( $value ) ) {
				$_link_attr[ $attr ] = null;
			}
		}

		$link_attr = apply_filters( 'nav_menu_link_attributes', $_link_attr, $item, $args );


		// Add UIKit active class.
		if ( in_array( 'current-menu-item', $classes ) ) {
			$item_attr['class'][] = 'uk-active';
		}

		// Add UIKit parent attributes.
		if ( $args->beans_start_level == $depth && in_array( 'menu-item-has-children', $classes ) ) {

			$item_attr['class'][] = 'uk-parent';

			if ( beans_get( 'beans_type', $args ) == 'navbar' ) {

				$item_attr['data-uk-dropdown'] = '';
				$child_indicator = true;

			}
		}

		// Implode to avoid empty spaces.
		$item_attr['class'] = implode( ' ', array_filter( $item_attr['class'] ) );

		// Set to null if empty to avoid outputing empty class html attribute.
		if ( ! $item_attr['class'] ) {
			$item_attr['class'] = null;
		}

		$output .= beans_open_markup( "beans_menu_item[_{$item_id}]", 'li', $item_attr, $item, $depth, $args );

			$item_output = $args->before;

				$item_output .= beans_open_markup( "beans_menu_item_link[_{$item_id}]", 'a', $link_attr, $item, $depth, $args );

					$item_output .= beans_open_markup( "beans_menu_item_span[_{$item_id}]", 'span', array( 'itemprop' => 'name' ));

						$item_output .= beans_output( "beans_menu_item_text[_{$item_id}]", $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after );

					$item_output .= beans_close_markup( "beans_menu_item_span[_{$item_id}]", 'span');

					if ( isset( $child_indicator ) ) {

						$item_output .= beans_open_markup( "beans_menu_item_child_indicator[_{$item_id}]", 'i', array( 'class' => 'uk-icon-caret-down uk-margin-small-left' ), $item, $depth, $args );
						$item_output .= beans_close_markup( "beans_menu_item_child_indicator[_{$item_id}]", 'i', $item, $depth, $args );

					}

				$item_output .= beans_close_markup( "beans_menu_item_link[_{$item_id}]", 'a', $link_attr, $item, $depth, $args );

			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

	}

	/**
	 * Extend WordPress end menu elements.
	 *
	 * @ignore
	 */
	function end_el( &$output, $item, $depth = 0, $args = array() ) {

		// Stop here if the depth is smaller than starting depth.
		if ( $depth < $args->beans_start_level ) {
			return;
		}

		$item_id = $item->ID;

		$output .= beans_close_markup( "beans_menu_item[_{$item_id}]", 'li', $item, $depth, $args );

	}
}
