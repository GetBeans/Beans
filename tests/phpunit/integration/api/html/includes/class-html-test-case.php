<?php
/**
 * Test Case for Beans' HTML API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\HTML\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class HTML_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\HTML\Includes
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
	 * Array of attachments to test.
	 *
	 * @var array
	 */
	protected static $test_attachments;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_markup      = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-markup.php';
		static::$test_attributes  = array_filter(
			static::$test_markup,
			function( $markup ) {
				return isset( $markup['attributes'] );
			}
		);
		static::$test_attachments = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-attachment.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Reset the test fixtures.
		reset( static::$test_markup );
		reset( static::$test_attributes );
		reset( static::$test_attachments );
	}

	/**
	 * Convert an array of attributes into a combined HTML string.
	 *
	 * @since 1.5.0
	 *
	 * @param array $attributes The given attributes to combine.
	 *
	 * @return string
	 */
	public function convert_attributes_into_html( array $attributes ) {
		$html = '';

		foreach ( $attributes as $attribute => $value ) {
			$html .= $attribute . '="' . $value . '" ';
		}

		return rtrim( $html );
	}
}
