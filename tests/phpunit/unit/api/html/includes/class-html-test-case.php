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
use Brain\Monkey;

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
	protected function setUp() {
		parent::setUp();

		$this->setup_function_mocks();
		$this->setup_common_wp_stubs();

		$this->load_original_functions(
			[
				'api/html/class-beans-attribute.php',
				'api/html/functions.php',
				'api/html/accessibility.php',
				'api/filters/functions.php',
				'api/layout/functions.php',
				'api/widget/functions.php',
			]
		);

		// Reset the test fixtures.
		reset( static::$test_markup );
		reset( static::$test_attributes );
		reset( static::$test_attachments );
	}

	/**
	 * Setup dependency function mocks.
	 */
	protected function setup_function_mocks() {
		Monkey\Functions\when( 'beans_esc_attributes' )->alias( [ $this, 'convert_attributes_into_html' ] );
		Monkey\Functions\when( 'beans_add_smart_action' )->justReturn();
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
