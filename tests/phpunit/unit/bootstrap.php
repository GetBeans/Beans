<?php
/**
 * Bootstraps the Beans Unit Tests.
 *
 * @package     Beans\Framework\Tests\Unit
 * @since       1.5.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 */

if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
	die( 'Beans Unit Tests require PHP 5.6 or higher.' );
}

define( 'BEANS_TESTS_DIR', __DIR__ );
define( 'BEANS_ROOT_DIR', dirname( dirname( dirname( __DIR__ ) ) ) . DIRECTORY_SEPARATOR );
define( 'BEANS_TESTS_LIB_DIR', BEANS_ROOT_DIR . 'lib' . DIRECTORY_SEPARATOR );

// Let's define ABSPATH as it is in WordPress, i.e. relative to the filesystem's WordPress root path.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( dirname( BEANS_ROOT_DIR ) ) ) . '/' ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound - ABSPATH is required.
}

// Time to load Composer's autoloader.
$beans_autoload_path = BEANS_ROOT_DIR . 'vendor/';

if ( ! file_exists( $beans_autoload_path . 'autoload.php' ) ) {
	die( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.' );
}
require_once $beans_autoload_path . 'autoload.php';
unset( $beans_autoload_path );

require_once BEANS_TESTS_DIR . '/class-test-case.php';
