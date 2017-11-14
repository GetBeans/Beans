<?php
/**
 * Bootstraps the Beans Tests.
 *
 * @package     Beans\BeansFramework\Tests
 * @since       1.5.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 */

if ( version_compare( phpversion(), '5.3.0', '<' ) ) {
	die( 'Beans Unit Tests require PHP 5.3 or higher.' );
}

define( 'BEANS_TESTS_DIR', dirname( __FILE__ ) );

define( 'BEANS_TESTS_LIB_DIR', BEANS_TESTS_DIR . '/../lib/' );

/**
 * You can turn on or off the integration tests.  Integration tests cause WordPress to load and run.
 * PHP UnitTests, on the other hand, do not load WordPress, using Brain Monkey instead.
 */
define( 'BEANS_RUN_INTEGRATION_TESTS', false );

/**
 * Time to load Composer's autoloader.
 */
$beans_autoload_path = dirname( __DIR__ ) . '/vendor/';
if ( ! file_exists( $beans_autoload_path . 'autoload.php' ) ) {
	die( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.' );
}
require_once $beans_autoload_path . 'autoload.php';
unset( $beans_autoload_path );
