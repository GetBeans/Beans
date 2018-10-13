<?php
/**
 * Test Case for Beans' Options API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Options\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Options\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class Options_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Options\Includes
 */
abstract class Options_Test_Case extends Test_Case {

	/**
	 * An array of test data.
	 *
	 * @var array
	 */
	protected static $test_data;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-options.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions(
			[
				'api/utilities/functions.php',
				'api/options/class-beans-options.php',
				'api/options/functions.php',
				'api/fields/functions.php',
			]
		);

		$this->setup_function_mocks();
		$this->setup_common_wp_stubs();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		global $wp_meta_boxes;
		$wp_meta_boxes = []; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Resetting global here for tests.

		parent::tearDown();
	}

	/**
	 * Setup dependency function mocks.
	 */
	protected function setup_function_mocks() {
		Monkey\Functions\when( 'add_meta_box' )->alias(
			function( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
				global $wp_meta_boxes;

				if ( empty( $screen ) ) {
					$screen = 'beans';
				}

			   	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Mocking global here for tests.
				$wp_meta_boxes = [
					$screen => [
						$context => [
							$priority => [
								$id => [
									'id'       => $id,
									'title'    => $title,
									'callback' => $callback,
									'args'     => $callback_args,
								],
							],
						],
					],
				];
			}
		);
	}
}
