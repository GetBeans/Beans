<?php
/**
 * Test Case for Beans' HTML API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML\Includes;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Abstract Class HTML_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Includes
 */
abstract class HTML_Test_Case extends Test_Case {

	/**
	 * An array of markup to test.
	 *
	 * @var array
	 */
	protected static $test_markup;

	/**
	 * An array of attributes to test.
	 *
	 * @var array
	 */
	protected static $test_attributes;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_markup     = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-markup.php';
		static::$test_attributes = array_filter( static::$test_markup, function( $markup ) {
			return isset( $markup['attributes'] );
		} );

		require_once BEANS_TESTS_LIB_DIR . 'api/html/class-beans-attribute.php';
		require_once BEANS_TESTS_LIB_DIR . 'api/html/functions.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/filters/functions.php',
		) );
	}
}
