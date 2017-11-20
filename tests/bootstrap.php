<?php
/**
 * Bootstraps the Beans Tests.
 *
 * @package     Beans\Framework\Tests
 * @since       1.5.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 */

if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
	die( 'Beans Unit Tests require PHP 5.6 or higher.' );
}

define( 'BEANS_TESTS_DIR', __DIR__ );

define( 'BEANS_TESTS_LIB_DIR', BEANS_TESTS_DIR . '/../lib/' );

/**
 * Time to load Composer's autoloader.
 */
$beans_autoload_path = dirname( __DIR__ ) . '/vendor/';

if ( ! file_exists( $beans_autoload_path . 'autoload.php' ) ) {
	die( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.' );
}
require_once $beans_autoload_path . 'autoload.php';
unset( $beans_autoload_path );

require_once BEANS_TESTS_DIR . '/unit-tests/class-test-case.php';
