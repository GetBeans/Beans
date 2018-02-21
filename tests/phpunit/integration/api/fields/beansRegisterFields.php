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

require_once __DIR__ . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansRegisterFields
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansRegisterFields extends Fields_Test_Case {

	/**
	 * Test beans_register_fields() should return false when there are no fields.
	 */
	public function test_should_return_false_when_no_fields() {
		$this->assertFalse( beans_register_fields( array(), '', '' ) );
		$this->assertFalse( beans_register_fields( array(), 'post_meta', 'tm-beans' ) );
	}

	/**
	 * Test beans_register_fields() should register the fields.
	 */
	public function test_should_register_fields() {

		foreach ( static::$test_data as $test_data ) {
			$this->assertTrue( beans_register_fields( $test_data['fields'], 'beans_tests', $test_data['section'] ) );

			// Check what was registered.
			$registered = $this->get_reflective_property_value( 'registered' );
			$this->assertArrayHasKey( 'beans_tests', $registered );
			$this->assertArrayHasKey( $test_data['section'], $registered['beans_tests'] );

			foreach ( $test_data['fields'] as $index => $field ) {
				$expected          = array_merge( array(
					'label'       => false,
					'description' => false,
					'default'     => false,
					'context'     => 'beans_tests',
					'attributes'  => array(),
					'db_group'    => false,
				), $field );
				$expected['name']  = 'beans_fields[' . $field['id'] . ']';
				$expected['value'] = $field['default'];

				$this->assertSame( $expected, $registered['beans_tests'][ $test_data['section'] ][ $index ] );
			}
		}
	}
}
