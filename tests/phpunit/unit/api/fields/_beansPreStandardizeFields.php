<?php
/**
 * Tests for _beans_pre_standardize_fields()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once __DIR__ . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansPreStandardizeFields
 *
 * @package Beans\Framework\Tests\Unit\API\Fields
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansPreStandardizeFields extends Fields_Test_Case {

	/**
	 * Test _beans_pre_standardize_fields() should pre-standardize single fields.
	 */
	public function test_should_pre_standardize_single_fields() {
		$fields = _beans_pre_standardize_fields( static::$test_data['single_fields']['fields'] );

		foreach ( static::$test_data['single_fields']['fields'] as $field ) {
			$this->assertArrayHasKey( $field['id'], $fields );
			$this->assertSame( $field, $fields[ $field['id'] ] );
		}
	}

	/**
	 * Test _beans_pre_standardize_fields() should pre-standardize a group of fields.
	 */
	public function test_should_pre_standardize_group_of_fields() {
		$actual = _beans_pre_standardize_fields( static::$test_data['group']['fields'] );

		foreach ( static::$test_data['group']['fields'] as $group ) {
			$this->assertArrayHasKey( $group['id'], $actual );

			$actual_fields = $actual[ $group['id'] ]['fields'];

			// Check each of the grouped fields.
			foreach ( $group['fields'] as $field ) {
				$this->assertArrayHasKey( $field['id'], $actual_fields );
				$this->assertSame( $field, $actual_fields[ $field['id'] ] );
			}
		}
	}
}
