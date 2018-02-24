<?php
/**
 * Test Case for Beans' Field API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Includes;

use WP_UnitTestCase;

/**
 * Abstract Class Fields_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Includes
 */
abstract class Fields_Test_Case extends WP_UnitTestCase {

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
		require_once BEANS_THEME_DIR . '/lib/api/fields/class-beans-fields.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::tearDown();

		// Reset the "registered" container.
		$registered = $this->get_reflective_property( 'registered' );
		$registered->setValue( new \_Beans_Fields(), array(
			'option'       => array(),
			'post_meta'    => array(),
			'term_meta'    => array(),
			'wp_customize' => array(),
		) );

		// Reset the other static properties.
		foreach ( array( 'field_types_loaded', 'field_assets_hook_loaded' ) as $property_name ) {
			$property = $this->get_reflective_property( $property_name );
			$property->setValue( new \_Beans_Fields(), array() );
		}
	}

	/**
	 * Get reflective access to the private method.
	 *
	 * @since 1.5.0
	 *
	 * @param string $method_name Method name for which to gain access.
	 *
	 * @return \ReflectionMethod
	 */
	protected function get_reflective_method( $method_name ) {
		$class  = new \ReflectionClass( '_Beans_Fields' );
		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method;
	}

	/**
	 * Get reflective access to the private property.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property Property name for which to gain access.
	 *
	 * @return \ReflectionProperty|string
	 */
	protected function get_reflective_property( $property ) {
		$class    = new \ReflectionClass( '_Beans_Fields' );
		$property = $class->getProperty( $property );
		$property->setAccessible( true );

		return $property;
	}

	/**
	 * Get the value of the private or protected property.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property Property name for which to gain access.
	 *
	 * @return mixed
	 */
	protected function get_reflective_property_value( $property ) {
		$reflective = $this->get_reflective_property( $property );
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

	/**
	 * Format the HTML by stripping out the whitespace between the HTML tags and then putting each tag on a separate
	 * line.
	 *
	 * Why? We can then compare the actual vs. expected HTML patterns without worrying about tabs, new lines, and extra
	 * spaces.
	 *
	 * @since 1.5.0
	 *
	 * @param string $html HTML to strip.
	 *
	 * @return string
	 */
	protected function format_the_html( $html ) {
		$html = trim( $html );

		// Strip whitespace between the tags.
		$html = preg_replace( '/(\>)\s*(\<)/m', '$1$2', $html );

		// Strip whitespace at the end of a tag.
		$html = preg_replace( '/(\>)\s*/m', '$1$2', $html );

		// Strip whitespace at the start of a tag.
		$html = preg_replace( '/\s*(\<)/m', '$1$2', $html );

		return str_replace( '>', ">\n", $html );
	}
}
