<?php
/**
 * Bootstraps the WordPress Integration Tests
 *
 * @package     Beans\Framework\Tests
 * @since       1.5.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 *
 * @group       integrationtests
 */

// Require patchwork early so that functions can be monkey patched in Unit tests.
require __DIR__ . '/../../vendor/antecedent/patchwork/Patchwork.php';

define( 'BEANS_INTEGRATION_TESTS_DIR', __DIR__ );

// Give access to tests_add_filter() function.
require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

define( 'THIS_THEME_DIR', basename( dirname( dirname( __DIR__ ) ) ) );

/**
 * Manually load the theme being tested.
 */
function beans_testing_manually_load_theme() {
	register_theme_directory( dirname( dirname( dirname( __FILE__ ) ) ) );
	add_filter( 'stylesheet', 'beans_testing_override_stylesheet' );
	add_filter( 'template', 'beans_testing_override_template' );
}

/**
 * Callback for changing the active stylesheet during tests.
 *
 * @param string $stylesheet Existing stylesheet name.
 *
 * @return string Amended stylesheet name.
 */
function beans_testing_override_stylesheet( $stylesheet ) {
	return wp_get_theme( THIS_THEME_DIR )->get_stylesheet();
}

/**
 * Callback for changing the active template during tests.
 *
 * @param string $template Existing template name.
 *
 * @return string Amended template name.
 */
function beans_testing_override_template( $template ) {
	return wp_get_theme( THIS_THEME_DIR )->get_template();
}

tests_add_filter( 'setup_theme', 'beans_testing_manually_load_theme' );

// Start up the WP testing environment.
require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';

require __DIR__ . '/class-test-case.php';
