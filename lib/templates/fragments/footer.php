<?php
/**
 * Echo footer fragments.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_footer', 'beans_footer_content' );
/**
 * Echo the footer content.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_footer_content() {
	beans_open_markup_e( 'beans_footer_credit', 'div', array( 'class' => 'uk-clearfix uk-text-small uk-text-muted' ) );

		beans_open_markup_e( 'beans_footer_credit_left', 'span', array( 'class' => 'uk-align-medium-left uk-margin-small-bottom' ) );

			beans_output_e(
				'beans_footer_credit_text',
				sprintf(
					// translators: Footer credits. Date followed by the name of the website.
					__( '&#x000A9; %1$s - %2$s. All rights reserved.', 'tm-beans' ),
					date( 'Y' ),
					get_bloginfo( 'name' )
				)
			);

		beans_close_markup_e( 'beans_footer_credit_left', 'span' );

		$framework_link = beans_open_markup(
			'beans_footer_credit_framework_link',
			'a',
			array(
				'href' => 'https://www.getbeans.io', // Automatically escaped.
				'rel'  => 'nofollow',
			)
		);

			$framework_link .= beans_output( 'beans_footer_credit_framework_link_text', 'Beans' );

		$framework_link .= beans_close_markup( 'beans_footer_credit_framework_link', 'a' );

		beans_open_markup_e( 'beans_footer_credit_right', 'span', array( 'class' => 'uk-align-medium-right uk-margin-bottom-remove' ) );

			beans_output_e(
				'beans_footer_credit_right_text',
				sprintf(
					// translators: Link to the Beans website.
					__( '%1$s theme for WordPress.', 'tm-beans' ),
					$framework_link
				)
			);

		beans_close_markup_e( 'beans_footer_credit_right', 'span' );

	beans_close_markup_e( 'beans_footer_credit', 'div' );
}

beans_add_smart_action( 'wp_footer', 'beans_replace_nojs_class' );
/**
 * Print inline JavaScript in the footer to replace the 'no-js' class with 'js'.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_replace_nojs_class() {
	?><script type="text/javascript">
		(function() {
			document.body.className = document.body.className.replace('no-js','js');
		}());
	</script>
	<?php
}
