<?php
/**
 * Test Case for Beans' Filter API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Filters\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Filters\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class Filters_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Filters\Includes
 */
abstract class Filters_Test_Case extends Test_Case {

	/**
	 * An array of filters to test.
	 *
	 * @var array
	 */
	protected static $test_filters;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_filters = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-filters.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->mock_filter_callbacks();

		$this->load_original_functions( [
			'api/filters/functions.php',
		] );
	}

	/**
	 * Reset the test fixture.
	 */
	protected function tearDown() {

		foreach ( static::$test_filters as $beans_id => $filter ) {

			if ( ! isset( $filter['callback'] ) ) {
				continue;
			}

			remove_filter( $filter['hook'], $filter['callback'], $filter['priority'] );
		}

		parent::tearDown();
	}

	/**
	 * Define the mocks for the filter callbacks.
	 */
	protected function mock_filter_callbacks() {
		Monkey\Functions\when( 'beans_test_the_content' )->alias( function ( $post_title, $post_id ) {
			return $post_title . '_' . $post_id;
		} );
		Monkey\Functions\when( 'beans_test_modify_widget_count' )->justReturn( 20 );
		Monkey\Functions\when( 'beans_test_query_args_base' )->justReturn( [ 'base' ] );
		Monkey\Functions\when( 'beans_test_query_args_main' )->alias( function ( $args ) {
			$args[] = '_main';
			return $args;
		} );
	}
}
