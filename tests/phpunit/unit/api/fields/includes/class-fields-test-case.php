<?php
/**
 * Test Case for Beans' Field API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Includes
 *
 * @since   1.5.0
 *
 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found -- Valid use cases to minimize work in tests.
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class Fields_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Includes
 */
abstract class Fields_Test_Case extends Test_Case {

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

		$this->load_original_functions(
			[
				'api/actions/functions.php',
				'api/fields/functions.php',
				'api/fields/class-beans-fields.php',
				'api/utilities/functions.php',
			]
		);

		$this->setup_function_mocks();
		$this->setup_common_wp_stubs();
	}

	/**
	 * Merge the given field with the default structure.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field     The given field to merge.
	 * @param bool  $set_value Optional. When true, sets the "value" to the "default".
	 *
	 * @return array
	 */
	protected function merge_field_with_default( array $field, $set_value = true ) {
		$merged_field         = array_merge(
			[
				'label'       => false,
				'description' => false,
				'default'     => false,
				'context'     => 'beans_tests',
				'attributes'  => [],
				'db_group'    => false,
			],
			$field
		);
		$merged_field['name'] = 'beans_fields[' . $field['id'] . ']';

		if ( $set_value ) {
			$merged_field['value'] = $field['default'];
		}

		return $merged_field;
	}

	/**
	 * Set up function mocks.
	 */
	protected function setup_function_mocks() {
		Monkey\Functions\when( 'checked' )->alias(
			function( $actual, $value ) {

				if ( $actual === $value ) {
					echo " checked='checked'";
				}
			}
		);

		Monkey\Functions\when( 'selected' )->alias(
			function( $actual, $value ) {

				if ( $actual === $value ) {
					echo " selected='selected'";
				}
			}
		);

		Monkey\Functions\when( '_n' )->alias(
			function( $single, $plural, $number ) {
					return $number > 1 ? $plural : $single;
			}
		);

		Monkey\Functions\when( 'beans_get' )->alias(
			function( $needle, $haystack = false, $default = null ) {
					$haystack = (array) $haystack;

					return isset( $haystack[ $needle ] ) ? $haystack[ $needle ] : $default;
			}
		);

		Monkey\Functions\when( 'beans_esc_attributes' )->alias(
			function( $attributes ) {
					$string = '';

				foreach ( (array) $attributes as $attribute => $value ) {

					if ( null === $value ) {
						continue;
					}

					$string .= $attribute . '="' . $value . '" ';
				}

					return trim( $string );
			}
		);

		Monkey\Functions\when( 'beans_add_smart_action' )->justReturn();
	}
}
