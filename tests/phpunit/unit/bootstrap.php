<?php
/**
 * Bootstraps the Beans Unit Tests.
 *
 * @package     Beans\Framework\Tests\Unit
 * @since       1.5.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 */

namespace Beans\Framework\Tests\Unit;

use function Beans\Framework\Tests\init_test_suite;

require_once dirname( dirname( __FILE__ ) ) . '/functions.php';
init_test_suite( 'unit' );

define( 'BEANS_TESTS_LIB_DIR', BEANS_THEME_DIR . 'lib' . DIRECTORY_SEPARATOR );
define( 'BEANS_API_PATH', BEANS_TESTS_LIB_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR );

// Let's define ABSPATH as it is in WordPress, i.e. relative to the filesystem's WordPress root path.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( dirname( BEANS_THEME_DIR ) ) ) . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Valid use case for our testing suite.
}

require_once BEANS_TESTS_DIR . '/class-test-case.php';
