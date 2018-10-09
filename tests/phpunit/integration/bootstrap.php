<?php
/**
 * Bootstraps the WordPress Integration Tests
 *
 * @package     Beans\Framework\Tests\Integration
 * @since       1.5.0
 * @link        https://www.getbeans.io
 * @license     GNU-2.0+
 */

namespace Beans\Framework\Tests\Integration;

use function Beans\Framework\Tests\init_test_suite;

if ( ! file_exists( '../../../wp-content' ) ) {
	trigger_error( 'Unable to run the integration tests, because the wp-content folder does not exist.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
}

require_once dirname( dirname( __FILE__ ) ) . '/functions.php';
init_test_suite( 'integration' );

define( 'WP_CONTENT_DIR', dirname( dirname( dirname( getcwd() ) ) ) . '/wp-content/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Our tests need to define this constant.

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . 'plugins/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- When this constant is not already defined, we define it here. It's a valid use case for our testing suite.
}

/**
 * Get the WordPress' tests suite directory.
 *
 * @since 1.5.0
 *
 * @return string
 */
function beans_get_wp_tests_dir() {
	$tests_dir = getenv( 'WP_TESTS_DIR' );

	// Travis CI & Vagrant SSH tests directory.
	if ( empty( $tests_dir ) ) {
		$tests_dir = '/tmp/wordpress-tests';
	}

	// If the tests' includes directory does not exist, try a relative path to the Core tests directory.
	if ( ! file_exists( $tests_dir . '/includes/' ) ) {
		$tests_dir = '../../../../tests/phpunit';
	}

	// Check it again. If it doesn't exist, stop here and post a message as to why we stopped.
	if ( ! file_exists( $tests_dir . '/includes/' ) ) {
		trigger_error( 'Unable to run the integration tests, because the WordPress test suite could not be located.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}

	// Strip off the trailing directory separator, if it exists.
	return rtrim( $tests_dir, DIRECTORY_SEPARATOR );
}

// Find the WP tests suite directory.
$beans_tests_dir = beans_get_wp_tests_dir();

// Give access to tests_add_filter() function.
require_once $beans_tests_dir . '/includes/functions.php';

/**
 * Register with "setup_theme" in the WP tests suite to set the themes directory
 * and load the Beans framework.
 */
tests_add_filter(
	'setup_theme',
	function() {
		register_theme_directory( WP_CONTENT_DIR . 'themes' );
		switch_theme( basename( BEANS_THEME_DIR ) );
	}
);

// Start up the WP testing environment.
require_once $beans_tests_dir . '/includes/bootstrap.php';

// Load the Integration Test Case.
require_once BEANS_TESTS_DIR . '/class-test-case.php';
