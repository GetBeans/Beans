<?php
/**
 * Beans' Test Suites' common functionality.
 *
 * @package Beans\Framework\Tests
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests;

/**
 * Initialize the test suite.
 *
 * @since 1.5.0
 *
 * @param string $test_suite Directory name of the test suite.
 *
 * @return void
 */
function init_test_suite( $test_suite ) {
	check_readiness();

	init_constants( $test_suite );

	// Load the files.
	$beans_root_dir = rtrim( BEANS_THEME_DIR, DIRECTORY_SEPARATOR );
	require_once $beans_root_dir . '/vendor/autoload.php';
	require_once __DIR__ . '/test-case-trait.php';

	// Load Patchwork before everything else in order to allow us to redefine WordPress and Beans functions.
	require_once $beans_root_dir . '/vendor/brain/monkey/inc/patchwork-loader.php';
}

/**
 * Check the system's readiness to run the tests.
 *
 * @since 1.5.0
 *
 * @return void
 */
function check_readiness() {

	if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
		trigger_error( 'Beans Unit Tests require PHP 5.6 or higher.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}

	if ( ! file_exists( dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php' ) ) {
		trigger_error( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}
}

/**
 * Initialize the constants.
 *
 * @since 1.5.0
 *
 * @param string $test_suite_folder Directory name of the test suite.
 *
 * @return void
 */
function init_constants( $test_suite_folder ) {
	define( 'BEANS_TESTS_DIR', __DIR__ . DIRECTORY_SEPARATOR . $test_suite_folder );

	$beans_root_dir = dirname( dirname( __DIR__ ) );
	if ( 'unit' === $test_suite_folder ) {
		$beans_root_dir .= DIRECTORY_SEPARATOR;
	}
	define( 'BEANS_THEME_DIR', $beans_root_dir );
}

/**
 * Resets Beans for the test suite.
 *
 * @since 1.5.0
 *
 * @return void
 */
function reset_beans() {
	get_beans_resetter()->reset();
}

/**
 * Gets the Bean's Resetter.
 *
 * @since 1.5.0
 *
 * @return Beans_Resetter
 */
function get_beans_resetter() {
	static $resetter;

	if ( is_null( $resetter ) ) {
		require_once __DIR__ . '/class-beans-resetter.php';
		$resetter = new Beans_Resetter();
	}

	return $resetter;
}
