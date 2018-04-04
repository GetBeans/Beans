<?php
/**
 * Test Case for Beans' Field API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Fields_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Includes
 */
abstract class Fields_Test_Case extends Test_Case {

	/**
	 * An array of test data.
	 *
	 * @var array
	 */
	protected static $test_data;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		require_once BEANS_THEME_DIR . '/lib/api/fields/class-beans-fields.php';

		add_action( 'beans_field_group_label', 'beans_field_label' );
		add_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
		add_action( 'beans_field_wrap_append_markup', 'beans_field_description' );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		remove_action( 'beans_field_group_label', 'beans_field_label' );
		remove_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
		remove_action( 'beans_field_wrap_append_markup', 'beans_field_description' );

		parent::tearDown();
	}

	/**
	 * Get the value of the private or protected property.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property Property name for which to gain access.
	 *
	 * @return mixed
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function get_reflective_property_value( $property ) {
		$reflective = $this->get_reflective_property( $property, '_Beans_Fields' );
		return $reflective->getValue( new \_Beans_Fields() );
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
		$field         = array_merge( array(
			'label'       => false,
			'description' => false,
			'default'     => false,
			'context'     => 'beans_tests',
			'attributes'  => array(),
			'db_group'    => false,
		), $field );
		$field['name'] = 'beans_fields[' . $field['id'] . ']';

		if ( 'group' === $field['type'] ) {

			foreach ( $field['fields'] as $index => $_field ) {
				$field['fields'][ $index ] = $this->merge_field_with_default( $_field, $set_value );
			}
		} elseif ( $set_value ) {
			$field['value'] = $field['default'];
		}

		return $field;
	}
}
