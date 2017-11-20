<?php
/**
 *
 * Load the API components.
 *
 * @since 1.5.0
 *
 * @package Beans\Framework\API
 */

// Stop here if the API was already loaded.
if ( defined( 'BEANS_API' ) ) {
	return;
}

// Declare Beans API.
define( 'BEANS_API', true );

// Mode.
if ( ! defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', false );
}

// Assets.
define( 'BEANS_MIN_CSS', SCRIPT_DEBUG ? '' : '.min' );
define( 'BEANS_MIN_JS', SCRIPT_DEBUG ? '' : '.min' );

// Path.
if ( ! defined( 'BEANS_API_PATH' ) ) {
	define( 'BEANS_API_PATH', wp_normalize_path( trailingslashit( dirname( __FILE__ ) ) ) );
}

define( 'BEANS_API_ADMIN_PATH', BEANS_API_PATH . 'admin/' );

// Load dependencies here, as these are used further down.
require_once BEANS_API_PATH . 'utilities/polyfills.php';
require_once BEANS_API_PATH . 'utilities/functions.php';
require_once BEANS_API_PATH . 'utilities/deprecated.php';
require_once BEANS_API_PATH . 'components.php';

// Url.
if ( ! defined( 'BEANS_API_URL' ) ) {
	define( 'BEANS_API_URL', beans_path_to_url( BEANS_API_PATH ) );
}

// Backwards compatibility constants.
define( 'BEANS_API_COMPONENTS_PATH', BEANS_API_PATH );
define( 'BEANS_API_COMPONENTS_ADMIN_PATH', BEANS_API_PATH . 'admin/' );
define( 'BEANS_API_COMPONENTS_URL', BEANS_API_URL );
