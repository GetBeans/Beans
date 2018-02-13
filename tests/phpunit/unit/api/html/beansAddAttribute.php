<?php
/**
 * Tests for beans_add_attribute()
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
 * Class Tests_BeansAddAttribute
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansAddAttribute extends HTML_Test_Case {

	/**
	 * Test beans_add_attribute() should add an empty value when no value is given.
	 */
	public function test_should_add_empty_value_when_no_value_given() {

		foreach ( static::$test_markup as $beans_id => $markup ) {
			$markup_attributes = isset( $markup['attributes'] ) ? $markup['attributes'] : array();
			$attributes        = beans_add_attribute( $beans_id, 'data-test', '' );
			$event             = $beans_id . '_attributes';

			// Set up the WordPress simulator.
			Monkey\Filters\expectApplied( $event )
				->once()
				->with( $markup_attributes )
				->andReturn( $attributes->add( $markup_attributes ) );

			// Run the tests.
			$this->assertTrue( has_filter( $event, array( $attributes, 'add' ), 10 ) );
			$expected              = $markup_attributes;
			$expected['data-test'] = '';
			$this->assertSame( $expected, apply_filters( $event, $markup_attributes ) );

			// Clean up.
			remove_filter( $event, array( $attributes, 'add' ), 10 );
		}
	}

	/**
	 * Test beans_add_attribute() should add the value when the attribute exists.
	 */
	public function test_should_add_value_when_attribute_exists() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {

			// Skip if it doesn't have a class attribute.
			if ( ! isset( $markup['attributes']['class'] ) ) {
				continue;
			}

			$attributes = beans_add_attribute( $beans_id, 'class', 'beans-test' );
			$event      = $beans_id . '_attributes';

			// Set up the WordPress simulator.
			Monkey\Filters\expectApplied( $event )
				->once()
				->with( $markup['attributes'] )
				->andReturn( $attributes->add( $markup['attributes'] ) );

			// Run the tests.
			$this->assertTrue( has_filter( $event, array( $attributes, 'add' ), 10 ) );
			$expected           = $markup['attributes'];
			$expected['class'] .= ' beans-test';
			$this->assertSame( $expected, apply_filters( $event, $markup['attributes'] ) );

			// Clean up.
			remove_filter( $event, array( $attributes, 'add' ), 10 );
		}
	}
}
