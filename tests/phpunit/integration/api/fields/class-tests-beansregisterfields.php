<?php
/**
 * Tests for beans_register_fields()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;
use _Beans_Fields;

require_once __DIR__ . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansRegisterFields
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   api
 * @group   api-fields
 */
class Tests_BeansRegisterFields extends Fields_Test_Case {

	/**
	 * Test beans_register_fields() should return false when there are no fields.
	 */
	public function test_should_return_false_when_no_fields() {
		$this->assertFalse( beans_register_fields( [], '', '' ) );
		$this->assertFalse( beans_register_fields( [], 'post_meta', 'tm-beans' ) );
	}

	/**
	 * Test beans_register_fields() should register the fields.
	 */
	public function test_should_register_fields() {
		$test_data = static::$test_data['single_fields'];

		$this->assertTrue( beans_register_fields( $test_data['fields'], 'beans_tests', $test_data['section'] ) );

		// Check what was registered.
		$registered_property = $this->get_reflective_property( 'registered', '_Beans_Fields' );
		$registered          = $registered_property->getValue( new _Beans_Fields() );

		$this->assertArrayHasKey( 'beans_tests', $registered );
		$this->assertArrayHasKey( $test_data['section'], $registered['beans_tests'] );

		foreach ( $test_data['fields'] as $index => $field ) {
			$expected = $this->merge_field_with_default( $field );
			$this->assertSame( $expected, $registered['beans_tests'][ $test_data['section'] ][ $index ] );
		}
	}
}
