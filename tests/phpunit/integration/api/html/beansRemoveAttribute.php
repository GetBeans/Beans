<?php
/**
 * Tests for beans_remove_attribute()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansRemoveAttribute
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansRemoveAttribute extends HTML_Test_Case {

	/**
	 * Test beans_remove_attribute() should return the instance and register the "remove" callback to the given ID.
	 */
	public function test_should_return_instance_and_register_callback_to_given_id() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_remove_attribute( $beans_id, 'data-test', 'test' );

			// Check that it returns an instance of _Beans_Attribute.
			$this->assertInstanceOf( \_Beans_Attribute::class, $instance );

			// Check that the object's "remove" method is registered to the filter event for the given ID.
			$this->assertSame( 10, has_filter( "{$beans_id}_attributes", [ $instance, 'remove' ], 10 ) );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'remove' ] );
		}
	}

	/**
	 * Test the end result of beans_remove_attribute() by firing the expected filter event for the given ID. Test
	 * should return the original attributes when the target attribute does not exist, meaning there's nothing to
	 * remove in the given attributes.
	 */
	public function test_should_return_original_attributes_when_target_attribute_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_remove_attribute( $beans_id, 'data-test', 'test' );

			// Check that the new attribute does not exist yet.
			$this->assertArrayNotHasKey( 'data-test', $markup['attributes'] );

			// Fire the event to run the remove.
			$actual = apply_filters( "{$beans_id}_attributes", $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Run the full systems test by applying the filter.
			$this->assertSame( $markup['attributes'], $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'remove' ] );
		}
	}

	/**
	 * Test the end result of beans_remove_attribute() by firing the expected filter event for the given ID. Test
	 * should remove the attribute when the given value is null.
	 */
	public function test_should_remove_attribute_when_value_is_null() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name     = key( $markup['attributes'] );
			$instance = beans_remove_attribute( $beans_id, $name, null );

			// Fire the event to run the remove.
			$actual = apply_filters( "{$beans_id}_attributes", $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Check that the attribute is removed.
			$this->assertArrayNotHasKey( $name, $actual );

			// Check that only that attribute is affected.
			$expected = $markup['attributes'];
			unset( $expected[ $name ] );
			$this->assertSame( $expected, $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'remove' ], 10 );
		}
	}

	/**
	 * Test the end result of beans_remove_attribute() by firing the expected filter event for the given ID. Test
	 * should remove the given value from the attribute.
	 */
	public function test_should_remove_the_given_value_from_attribute() {
		$attributes = [
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		];

		$instance = beans_remove_attribute( 'beans_test', 'class', 'uk-panel-box' );

		// Fire the event to run the remove.
		$actual = apply_filters( 'beans_test_attributes', $attributes );

		// Check that it removed only that attribute value.
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );
		$this->assertSame( 'uk-article  category-beans', $actual['class'] );

		// Check that only the class attribute is affected.
		$expected          = $attributes;
		$expected['class'] = 'uk-article  category-beans';
		$this->assertSame( $expected, $actual );

		// Clean up.
		remove_filter( 'beans_test_attributes', [ $instance, 'remove' ], 10 );
	}

	/**
	 * Test the end result of beans_remove_attribute() by firing the expected filter event for the given ID. Test
	 * should return an empty array when an empty array is given. Why? There is nothing to remove, as there are no
	 * attributes.
	 */
	public function test_should_return_empty_array_when_empty_array_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name     = key( $markup['attributes'] );
			$value    = current( $markup['attributes'] );
			$instance = beans_remove_attribute( $beans_id, $name, $value );

			// Fire the event to run the remove.
			$actual = apply_filters( "{$beans_id}_attributes", [] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Check that an empty array is returned.
			$this->assertSame( [], $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'remove' ], 10 );
		}
	}
}
