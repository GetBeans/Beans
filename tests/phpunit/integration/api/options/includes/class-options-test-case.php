<?php
/**
 * Test Case for Beans' Options API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Options\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Options\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Options_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Options\Includes
 */
abstract class Options_Test_Case extends Test_Case {

	/**
	 * An array of test data.
	 *
	 * @var array
	 */
	protected static $test_data;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-options.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		reset( static::$test_data );

		require_once BEANS_THEME_DIR . '/lib/api/options/class-beans-options.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {

		// Let's clean up after the test.
		$this->clean_up_global_scope();

		global $wp_meta_boxes;
		$wp_meta_boxes = array(); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Resetting global here for tests.

		parent::tearDown();
	}

	/**
	 * Go to the Settings Page.
	 */
	protected function go_to_settings_page() {
		set_current_screen( 'themes.php?page=beans_settings' );

		$this->assertTrue( is_admin() );
	}
}
