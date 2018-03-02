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
use Brain\Monkey\Functions;

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
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		require_once dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'stubs/functions.php';

		static::$test_filters = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-filters.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/utilities/functions.php',
		) );
	}

	/**
	 * Reset the test fixture.
	 */
	protected function tearDown() {
		parent::tearDown();

		foreach ( static::$test_filters as $beans_id => $filter ) {

			if ( ! isset( $filter['callback'] ) ) {
				continue;
			}

			remove_filter( $filter['hook'], $filter['callback'], $filter['priority'] );
		}
	}
}
