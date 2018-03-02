<?php
/**
 * Echo the widget area and widget loop structural markup. It also calls the widget area and widget loop
 * action hooks.
 *
 * @package Beans\Framework\Templates\Structure
 *
 * @since   1.0.0
 */

// This includes everything added to wp hooks before the widgets.
echo beans_get_widget_area( 'before_widgets' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Widget area has to be echoed.

	// phpcs:disable Generic.WhiteSpace.ScopeIndent -- Code structure mirrors HTML markup.
	if ( 'grid' === beans_get_widget_area( 'beans_type' ) ) {
		beans_open_markup_e(
			'beans_widget_area_grid' . _beans_widget_area_subfilters(),
			'div',
			array(
				'class'               => 'uk-grid',
				'data-uk-grid-margin' => '',
			)
		);
	}

	if ( 'offcanvas' === beans_get_widget_area( 'beans_type' ) ) {

		beans_open_markup_e(
			'beans_widget_area_offcanvas_wrap' . _beans_widget_area_subfilters(),
			'div',
			array(
				'id'    => beans_get_widget_area( 'id' ), // Automatically escaped.
				'class' => 'uk-offcanvas',
			)
		);

			beans_open_markup_e( 'beans_widget_area_offcanvas_bar' . _beans_widget_area_subfilters(), 'div', array( 'class' => 'uk-offcanvas-bar' ) );
	}

		// Widgets.
		if ( beans_have_widgets() ) :

			/**
			 * Fires before widgets loop.
			 *
			 * This hook only fires if widgets exist.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_before_widgets_loop' );

				while ( beans_have_widgets() ) :
					beans_setup_widget();

					if ( 'grid' === beans_get_widget_area( 'beans_type' ) ) {
						beans_open_markup_e( 'beans_widget_grid' . _beans_widget_subfilters(), 'div', beans_widget_shortcodes( 'class=uk-width-medium-1-{count}' ) );
					}

						beans_open_markup_e( 'beans_widget_panel' . _beans_widget_subfilters(), 'div', beans_widget_shortcodes( 'class=tm-widget uk-panel widget_{type} {id}' ) );

							/**
							 * Fires in each widget panel structural HTML.
							 *
							 * @since 1.0.0
							 */
							do_action( 'beans_widget' );

						beans_close_markup_e( 'beans_widget_panel' . _beans_widget_subfilters(), 'div' );

					if ( 'grid' === beans_get_widget_area( 'beans_type' ) ) {
						beans_close_markup_e( 'beans_widget_grid' . _beans_widget_subfilters(), 'div' );
					}
				endwhile;

			/**
			 * Fires after the widgets loop.
			 *
			 * This hook only fires if widgets exist.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_after_widgets_loop' );
		else :

			/**
			 * Fires if no widgets exist.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_no_widget' );
		endif;

	if ( 'offcanvas' === beans_get_widget_area( 'beans_type' ) ) {

			beans_close_markup_e( 'beans_widget_area_offcanvas_bar' . _beans_widget_area_subfilters(), 'div' );

		beans_close_markup_e( 'beans_widget_area_offcanvas_wrap' . _beans_widget_area_subfilters(), 'div' );
	}

	if ( 'grid' === beans_get_widget_area( 'beans_type' ) ) {
		beans_close_markup_e( 'beans_widget_area_grid' . _beans_widget_area_subfilters(), 'div' );
	}

// This includes everything added to wp hooks after the widgets.
echo beans_get_widget_area( 'after_widgets' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Widget area has to be echoed.

// phpcs:enable Generic.WhiteSpace.ScopeIndent -- Code structure mirrors HTML markup.
