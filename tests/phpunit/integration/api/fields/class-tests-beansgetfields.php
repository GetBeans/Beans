<?php
/**
 * Tests for beans_get_fields()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once __DIR__ . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansGetFields
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   api
 * @group   api-fields
 */
class Tests_BeansGetFields extends Fields_Test_Case {

	/**
	 * Test beans_get_fields() should return the registered fields.
	 */
	public function test_should_return_registered_fields() {

		foreach ( static::$test_data as $test_data ) {
			$data_set = [
				'beans_tests' => [
					$test_data['section'] => $test_data['fields'],
				],
			];

			// Register the fields first.
			$registered = $this->get_reflective_property( 'registered', 'Beans_Fields' );
			$registered->setValue( new \Beans_Fields(), $data_set );

			$this->assertSame( $data_set['beans_tests'][ $test_data['section'] ], beans_get_fields( 'beans_tests', $test_data['section'] ) );
		}
	}
}
