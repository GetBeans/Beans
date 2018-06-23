<?php
/**
 * Handles Beans updates.
 *
 * @package Beans\Framework\Admin
 *
 * @since   1.0.0
 */

add_filter( 'site_transient_update_themes', 'beans_updater' );
/**
 * Retrieve product data from the Beans REST API.
 *
 * Data is cached in a transient for 24 hours. Product data will only be retrieved
 * if no transient is found to avoid long loading times.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @param object $value Update check object.
 *
 * @return object Modified update check object.
 */
function beans_updater( $value ) {

	// Stop here if the current user is not a super admin.
	if ( ! is_super_admin() ) {
		return;
	}

	$data  = get_site_transient( 'beans_updater' );
	$theme = wp_get_theme( 'tm-beans' );

	if ( ! $theme->exists() ) {
		return $value;
	}

	$current_version = $theme->get( 'Version' );

	// Query the Beans REST API if the transient is expired.
	if ( empty( $data ) ) {
		$response = wp_remote_get( 'https://www.getbeans.io/rest-api/' );

		// Retrieve data from the body and decode json format.
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		// Stop here if there is an error, set a temporary transient and bail out.
		if ( is_wp_error( $response ) || isset( $data['error'] ) ) {
			set_site_transient( 'beans_updater', array( 'version' => $current_version ), 30 * MINUTE_IN_SECONDS );
			return $value;
		}

		set_site_transient( 'beans_updater', $data, 24 * HOUR_IN_SECONDS );
	}

	// Return data if Beans is not up to date.
	if ( version_compare( $current_version, beans_get( 'version', $data ), '<' ) ) {
		$value->response[ $data['path'] ] = array(
			'slug'        => $data['slug'],
			'name'        => $data['name'],
			'url'         => $data['changelog_url'],
			'package'     => $data['download_url'],
			'new_version' => $data['version'],
			'tested'      => $data['tested'],
			'requires'    => $data['requires'],
		);
		return $value;
	}

	return $value;
}

add_action( 'load-update-core.php', 'beans_updater_clear_transient' );
/**
 * Clear updater transient.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @return void
 */
function beans_updater_clear_transient() {
	delete_site_transient( 'beans_updater' );
}
