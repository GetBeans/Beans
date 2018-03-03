<?php
/**
 * Echo header fragments.
 *
 * @package Beans\Framework\Templates\Fragments
 *
 * @since   1.0.0
 */

beans_add_smart_action( 'beans_head', 'beans_head_meta', 0 );
/**
 * Echo head meta.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_head_meta() {
	?>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php
}

beans_add_smart_action( 'wp_head', 'beans_head_pingback' );
/**
 * Echo head pingback.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_head_pingback() {
	?>
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
	<?php
}

beans_add_smart_action( 'wp_head', 'beans_favicon' );
/**
 * Echo head favicon if no icon was added via the customizer.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_favicon() {

	// Stop here if and icon was added via the customizer.
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}

	$url = file_exists( get_stylesheet_directory() . '/favicon.ico' ) ? get_stylesheet_directory_uri() . '/favicon.ico' : BEANS_URL . 'favicon.ico';

	beans_selfclose_markup_e(
		'beans_favicon',
		'link',
		array(
			'rel'  => 'Shortcut Icon',
			'href' => $url, // Automatically escaped.
			'type' => 'image/x-icon',
		)
	);
}

beans_add_smart_action( 'wp_head', 'beans_header_image' );
/**
 * Print the header image css inline in the header.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_header_image() {
	$header_image = get_header_image();

	if ( ! current_theme_supports( 'custom-header' ) || ! $header_image || empty( $header_image ) ) {
		return;
	}

	?>
	<style type="text/css">
		.tm-header {
			background-image: url(<?php echo esc_url( $header_image ); ?>);
			background-position: 50% 50%;
			background-size: cover;
			background-repeat: no-repeat;
		}
	</style>
	<?php
}

beans_add_smart_action( 'beans_header', 'beans_site_branding' );
/**
 * Echo header site branding.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_site_branding() {
	beans_open_markup_e(
		'beans_site_branding',
		'div',
		array(
			'class' => 'tm-site-branding uk-float-left' . ( ! get_bloginfo( 'description' ) ? ' uk-margin-small-top' : null ),
		)
	);

		beans_open_markup_e(
			'beans_site_title_link',
			'a',
			array(
				'href'     => home_url(), // Automatically escaped.
				'rel'      => 'home',
				'itemprop' => 'headline',
			)
		);

			$logo = get_theme_mod( 'beans_logo_image', false );

			// phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact -- Code structure mimics HTML markup.
			if ( $logo ) {
				beans_selfclose_markup_e(
					'beans_logo_image',
					'img',
					array(
						'class' => 'tm-logo',
						'src'   => $logo, // Automatically escaped.
						'alt'   => get_bloginfo( 'name' ), // Automatically escaped.
					)
				);
			} else {
				beans_output_e( 'beans_site_title_text', get_bloginfo( 'name' ) );
			}

		// phpcs:enable Generic.WhiteSpace.ScopeIndent.IncorrectExact -- Code structure mimics HTML markup.
		beans_close_markup_e( 'beans_site_title_link', 'a' );

	beans_close_markup_e( 'beans_site_branding', 'div' );
}

beans_add_smart_action( 'beans_site_branding_append_markup', 'beans_site_title_tag' );
/**
 * Echo header site title tag.
 *
 * @since 1.0.0
 *
 * @return void
 */
function beans_site_title_tag() {
	// Stop here if there isn't a description.
	$description = get_bloginfo( 'description' );

	if ( ! $description ) {
		return;
	}

	beans_open_markup_e(
		'beans_site_title_tag',
		'span',
		array(
			'class'    => 'tm-site-title-tag uk-text-small uk-text-muted uk-display-block',
			'itemprop' => 'description',
		)
	);

		beans_output_e( 'beans_site_title_tag_text', $description );

	beans_close_markup_e( 'beans_site_title_tag', 'span' );
}
