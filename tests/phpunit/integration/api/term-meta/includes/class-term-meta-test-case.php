<?php
/**
 * Test Case for Beans Term_Meta API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Term_Meta_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta\Includes
 */
abstract class Term_Meta_Test_Case extends Test_Case {

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

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
	}
}
