<?php
/**
 * Test Case for Beans' Term_Meta API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class Term_Meta_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta\Includes
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

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( [
			'api/actions/functions.php',
			'api/term-meta/class-beans-term-meta.php',
			'api/term-meta/functions.php',
			'api/term-meta/functions-admin.php',
			'api/fields/functions.php',
			'api/utilities/functions.php',
		] );

		$this->setup_common_wp_stubs();
		Monkey\Functions\when( '_beans_pre_standardize_fields' )->returnArg();
	}
}
