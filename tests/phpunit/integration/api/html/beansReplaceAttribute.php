<?php
/**
 * Tests for beans_replace_attribute()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansReplaceAttribute
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansReplaceAttribute extends HTML_Test_Case {

	/**
	 * Test beans_replace_attribute() should return the instance and register the "replace" callback to the given ID.
	 */
	public function test_should_return_instance_and_register_callback_to_given_id() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_replace_attribute( $beans_id, key( $markup['attributes'] ), current( $markup['attributes'] ), 'test' );

			// Check that it returns an instance of _Beans_Attribute.
			$this->assertInstanceOf( \_Beans_Attribute::class, $instance );

			// Check that the object's "replace" method is registered to the filter event for the given ID.
			$this->assertSame( 10, has_filter( "{$beans_id}_attributes", [ $instance, 'replace' ], 10 ) );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'replace' ] );
		}
	}

	/**
	 * Test the end result of beans_replace_attribute() by firing the expected filter event for the given ID. Test should replace
	 * an existing attribute value.
	 */
	public function test_should_replace_existing_attribute_value() {
		$attributes = [
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'https://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		];

		$instance = beans_replace_attribute( 'beans_post', 'class', 'uk-panel-box', 'beans-test' );

		// Check that the attribute does not contain the new value.
		$this->assertNotContains( 'beans-test', $attributes['class'] );

		// Fire the event to run the replace.
		$actual = apply_filters( 'beans_post_attributes', $attributes );

		// Check that the attribute is added with the given value.
		$this->assertContains( 'beans-test', $actual['class'] );
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );

		// Clean up.
		remove_filter( 'beans_post_attributes', [ $instance, 'replace' ], 10 );
	}

	/**
	 * Test the end result of beans_replace_attribute() by firing the expected filter event for the given ID. Test should replace
	 * (overwrite) all attribute's values with the new value when the target value is empty (null, empty string, etc.).
	 */
	public function test_should_overwrite_attribute_values_when_target_value_is_empty() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name = key( $markup['attributes'] );
			$hook = "{$beans_id}_attributes";

			// Check when both target value and new value are null.
			$instance = beans_replace_attribute( $beans_id, $name, null );
			$actual   = apply_filters( $hook, $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.
			$this->assertNull( $actual[ $name ] );
			remove_filter( $hook, [ $instance, 'replace' ], 10 );

			// Check when the target value is null.
			$instance = beans_replace_attribute( $beans_id, $name, null, '' );
			$actual   = apply_filters( $hook, $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.
			$this->assertSame( '', $actual[ $name ] );
			remove_filter( $hook, [ $instance, 'replace' ], 10 );

			// Check when the target value is an empty string.
			$instance = beans_replace_attribute( $beans_id, $name, '', 'foo' );
			$actual   = apply_filters( $hook, $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.
			$this->assertSame( 'foo', $actual[ $name ] );
			remove_filter( $hook, [ $instance, 'replace' ], 10 );

			// Check when the target value is false.
			$instance = beans_replace_attribute( $beans_id, $name, false, 'foo' );
			$actual   = apply_filters( $hook, $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.
			$this->assertSame( 'foo', $actual[ $name ] );
			remove_filter( $hook, [ $instance, 'replace' ], 10 );
		}
	}

	/**
	 * Test the end result of beans_replace_attribute() by firing the expected filter event for the given ID. Test should add the
	 * attribute when it does not exists in the given attributes.
	 */
	public function test_should_add_attribute_when_it_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_replace_attribute( $beans_id, 'data-test', 'foo', 'beans-test' );

			// Check that the attribute does not exist.
			$this->assertArrayNotHasKey( 'data-test', $markup['attributes'] );

			// Fire the event to run the replace.
			$actual = apply_filters( "{$beans_id}_attributes", $markup['attributes'] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Check that the new attribute is added.
			$this->assertArrayHasKey( 'data-test', $actual );
			$this->assertSame( 'beans-test', $actual['data-test'] );

			// Check that only the data-test attribute is affected.
			$expected              = $markup['attributes'];
			$expected['data-test'] = 'beans-test';
			$this->assertSame( $expected, $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'replace' ], 10 );
		}
	}

	/**
	 * Test the end result of beans_replace_attribute() by firing the expected filter event for the given ID. Test should add the
	 * attribute when an empty array is given.
	 */
	public function test_should_add_attribute_when_an_empty_array_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$name     = key( $markup['attributes'] );
			$value    = current( $markup['attributes'] );
			$instance = beans_replace_attribute( $beans_id, $name, $value, 'beans-test' );

			// Fire the event to run the replace.
			$actual = apply_filters( "{$beans_id}_attributes", [] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Check that it did add the attribute.
			$this->assertArrayHasKey( $name, $actual );
			$this->assertSame( [ $name => 'beans-test' ], $actual );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", [ $instance, 'replace' ], 10 );
		}
	}
}
