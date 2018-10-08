<?php
/**
 * Tests for beans_add_attribute()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAddAttribute
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAddAttribute extends HTML_Test_Case {

	/**
	 * Test beans_add_attribute() should return the instance and register the "add" callback to the given ID.
	 */
	public function test_should_return_instance_and_register_callback_to_given_id() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_add_attribute( $beans_id, 'data-test', 'test' );

			// Check that it returns an instance of Beans_Attribute.
			$this->assertInstanceOf( \Beans_Attribute::class, $instance );

			// Check that the object's "add" method is registered to the filter event for the given ID.
			$this->assertSame( 10, has_filter( "{$beans_id}_attributes", [ $instance, 'add' ], 10 ) );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'add' ] );
		}
	}

	/**
	 * Test the end result of beans_add_attribute() by firing the expected filter event for the given ID. Test should add the
	 * attribute when it does not exist in the given attributes.
	 */
	public function test_should_add_the_attribute_when_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_add_attribute( $beans_id, 'data-test', 'foo' );

			// Check that the attribute does not exist before we add it.
			$this->assertArrayNotHasKey( 'data-test', $markup['attributes'] );

			// Fire the event to do the add.
			$actual = apply_filters( "{$beans_id}_attributes", $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Check that the attribute is added with the given value.
			$expected              = $markup['attributes'];
			$expected['data-test'] = 'foo';
			$this->assertSame( $expected, $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'add' ], 10 );
		}
	}

	/**
	 * Test the end result of beans_add_attribute() by firing the expected filter event for the given ID. Test should add the
	 * value to an existing attribute's values.
	 */
	public function test_should_add_value_to_existing_attribute_values() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name     = key( $markup['attributes'] );
			$instance = beans_add_attribute( $beans_id, $name, 'beans-test' );

			// Fire the event to do the add.
			$actual = apply_filters( "{$beans_id}_attributes", $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			$expected           = $markup['attributes'];
			$expected[ $name ] .= ' beans-test';
			$this->assertSame( $expected, $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'add' ], 10 );
		}
	}
}
