<?php
/**
 * Bootstraps the WordPress Integration Tests
 *
 * @package     Beans\Framework\Tests\Integration
 * @since       1.5.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 *
 * @group       integrationtests
 */

if ( ! file_exists( '../../../wp-content' ) ) {
	return;
}

define( 'BEANS_THEME_DIR', dirname( dirname( dirname( __DIR__ ) ) ) );
define( 'WP_CONTENT_DIR', dirname( dirname( dirname( getcwd() ) ) ) . '/wp-content/' ); // @codingStandardsIgnoreLine.

if ( defined( 'WP_CONTENT_DIR' ) && ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . 'plugins/' ); // @codingStandardsIgnoreLine.
}

$beans_tests_dir = getenv( 'WP_TESTS_DIR' );

// Travis CI & Vagrant SSH tests directory.
if ( empty( $beans_tests_dir ) ) {
	$beans_tests_dir = '/tmp/wordpress-tests';
}

// Relative path to Core tests directory.
if ( ! file_exists( $beans_tests_dir . '/includes/' ) ) {
	$beans_tests_dir = '../../../../tests/phpunit';
}

if ( ! file_exists( $beans_tests_dir . '/includes/' ) ) {
	trigger_error( 'Unable to locate wordpress-tests-lib', E_USER_ERROR ); // @codingStandardsIgnoreLine.
}

// Give access to tests_add_filter() function.
require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

/**
 * Loads theme.
 */
tests_add_filter( 'setup_theme', function() {
	register_theme_directory( WP_CONTENT_DIR . '/themes' );
	switch_theme( basename( BEANS_THEME_DIR ) );
} );

// Start up the WP testing environment.
require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';
