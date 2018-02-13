<?php
/**
 * Tests for beans_replace_attribute()
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
 * Class Tests_BeansReplaceAttribute
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansReplaceAttribute extends HTML_Test_Case {

	/**
	 * Test beans_add_attribute() should add the attribute when it does not exist.
	 */
	public function test_should_add_attribute_when_it_does_not_exist() {

		foreach ( static::$test_markup as $beans_id => $markup ) {
			$markup_attributes = isset( $markup['attributes'] ) ? $markup['attributes'] : array();
			$attributes        = beans_replace_attribute( $beans_id, 'data-test', '' );
			$hook              = $beans_id . '_attributes';

			// Set up the WordPress simulator.
			Monkey\Filters\expectApplied( $hook )
				->once()
				->with( $markup_attributes )
				->andReturn( $attributes->add( $markup_attributes ) );

			// Run the tests.
			$this->assertTrue( has_filter( $hook, array( $attributes, 'replace' ), 10 ) );
			$expected              = $markup_attributes;
			$expected['data-test'] = '';
			$this->assertSame( $expected, apply_filters( $hook, $markup_attributes ) ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $attributes, 'replace' ), 10 );
		}
	}

	/**
	 * Test beans_add_attribute() should replace an existing attribute value.
	 */
	public function test_should_replace_existing_attribute_value() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$value     = current( $markup['attributes'] );
			$attribute = key( $markup['attributes'] );

			$attributes = beans_replace_attribute( $beans_id, $attribute, $value, 'beans-test' );
			$hook       = $beans_id . '_attributes';

			// Set up the WordPress simulator.
			Monkey\Filters\expectApplied( $hook )
				->once()
				->with( $markup['attributes'] )
				->andReturn( $attributes->replace( $markup['attributes'] ) );

			// Run the tests.
			$this->assertTrue( has_filter( $hook, array( $attributes, 'replace' ), 10 ) );
			$expected               = $markup['attributes'];
			$expected[ $attribute ] = str_replace( $value, 'beans-test', $expected[ $attribute ] );
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // @codingStandardsIgnoreLine - WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound  The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $attributes, 'replace' ), 10 );
		}
	}
}
