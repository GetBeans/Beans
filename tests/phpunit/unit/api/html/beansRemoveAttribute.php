<?php
/**
 * Tests for beans_remove_attribute()
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansRemoveAttribute
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansRemoveAttribute extends HTML_Test_Case {

	/**
	 * Test beans_remove_attribute() should remove the attribute when null given.
	 */
	public function test_should_remove_attribute_when_null_given() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$attribute = key( $markup['attributes'] );

			$attributes = beans_remove_attribute( $beans_id, $attribute );
			$hook       = $beans_id . '_attributes';

			// Set up the WordPress simulator.
			Monkey\Filters\expectApplied( $hook )
				->once()
				->with( $markup['attributes'] )
				->andReturn( $attributes->remove( $markup['attributes'] ) );

			// Run the tests.
			$this->assertTrue( has_filter( $hook, array( $attributes, 'remove' ), 10 ) );
			$expected = $markup['attributes'];
			unset( $expected[ $attribute ] );
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $attributes, 'remove' ), 10 );
		}
	}

	/**
	 * Test beans_remove_attribute() should remove the given value from the attribute.
	 */
	public function test_should_remove_the_given_value_from_attribute() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$value     = current( $markup['attributes'] );
			$attribute = key( $markup['attributes'] );

			$attributes = beans_remove_attribute( $beans_id, $attribute, $value );
			$hook       = $beans_id . '_attributes';

			// Set up the WordPress simulator.
			Monkey\Filters\expectApplied( $hook )
				->once()
				->with( $markup['attributes'] )
				->andReturn( $attributes->remove( $markup['attributes'] ) );

			// Run the tests.
			$this->assertTrue( has_filter( $hook, array( $attributes, 'remove' ), 10 ) );
			$expected               = $markup['attributes'];
			$expected[ $attribute ] = str_replace( $value, '', $expected[ $attribute ] );
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $attributes, 'remove' ), 10 );
		}
	}
}
