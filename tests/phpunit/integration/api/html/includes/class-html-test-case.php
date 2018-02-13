<?php
/**
 * Tests Case for Beans' HTML API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\HTML\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML\Includes;

use WP_UnitTestCase;

/**
 * Abstract Class HTML_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\HTML\Includes
 */
abstract class HTML_Test_Case extends WP_UnitTestCase {

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
	}
}
