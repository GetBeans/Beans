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
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansReplaceAttribute extends HTML_Test_Case {

	/**
	 * Test beans_replace_attribute() should replace an existing attribute value.
	 */
	public function test_should_replace_existing_attribute_value() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$value     = current( $markup['attributes'] );
			$attribute = key( $markup['attributes'] );

			$attributes = beans_replace_attribute( $beans_id, $attribute, $value, 'beans-test' );
			$hook       = $beans_id . '_attributes';

			// Run the tests.
			$this->assertSame( 10, has_filter( $hook, array( $attributes, 'replace' ), 10 ) );
			$expected               = $markup['attributes'];
			$expected[ $attribute ] = str_replace( $value, 'beans-test', $expected[ $attribute ] );
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $attributes, 'replace' ), 10 );
		}
	}

	/**
	 * Test beans_replace_attribute() should replace all attribute values with new value when no value given.
	 */
	public function test_should_replace_all_attributes_when_no_value_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$value     = current( $markup['attributes'] );
			$attribute = key( $markup['attributes'] );

			$attributes = beans_replace_attribute( $beans_id, $attribute, null, 'beans-test' );
			$hook       = $beans_id . '_attributes';

			// Run the tests.
			$this->assertSame( 10, has_filter( $hook, array( $attributes, 'replace' ), 10 ) );
			$expected               = $markup['attributes'];
			$expected[ $attribute ] = str_replace( $value, 'beans-test', $expected[ $attribute ] );
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $attributes, 'replace' ), 10 );
		}
	}

	/**
	 * Test beans_replace_attribute() should replace all values with null when no value is passed.
	 */
	public function test_should_replace_all_values_with_null_when_no_value_passed() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$value     = current( $markup['attributes'] );
			$attribute = key( $markup['attributes'] );

			$attributes = beans_replace_attribute( $beans_id, $attribute, '' );
			$hook       = $beans_id . '_attributes';

			// Run the tests.
			$this->assertSame( 10, has_filter( $hook, array( $attributes, 'replace' ), 10 ) );
			$expected               = $markup['attributes'];
			$expected[ $attribute ] = null;
			$actual = apply_filters( $hook, $markup['attributes'] ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.
			$this->assertSame( $expected, $actual );
			$this->assertNotSame( $value, $actual[ $attribute ] );
			$this->assertNull( $actual[ $attribute ] );

			// Clean up.
			remove_filter( $hook, array( $attributes, 'replace' ), 10 );
		}
	}

	/**
	 * Test beans_replace_attribute() should replace all values when an empty value is given as the new value.
	 */
	public function test_should_replace_all_values_when_empty_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$value     = current( $markup['attributes'] );
			$attribute = key( $markup['attributes'] );

			$attributes = beans_replace_attribute( $beans_id, $attribute, null, '' );
			$hook       = $beans_id . '_attributes';

			// Run the tests.
			$this->assertSame( 10, has_filter( $hook, array( $attributes, 'replace' ), 10 ) );
			$expected               = $markup['attributes'];
			$expected[ $attribute ] = '';
			$actual = apply_filters( $hook, $markup['attributes'] ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.
			$this->assertSame( $expected, $actual );
			$this->assertNotSame( $value, $actual[ $attribute ] );
			$this->assertSame( '', $actual[ $attribute ] );

			// Clean up.
			remove_filter( $hook, array( $attributes, 'replace' ), 10 );
		}
	}
}
