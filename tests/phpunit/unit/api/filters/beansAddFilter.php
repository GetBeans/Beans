<?php
/**
 * Tests for beans_add_filter()
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Filters;

use Beans\Framework\Tests\Unit\API\Filters\Includes\Filters_Test_Case;

require_once __DIR__ . '/includes/class-filters-test-case.php';

/**
 * Class Tests_BeansAddFilter
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansAddFilter extends Filters_Test_Case {

	/**
	 * Test beans_add_filter() should add (register) the filter when callback is callable.
	 */
	public function test_should_add_the_filter_when_callable() {

		foreach ( static::$test_filters as $beans_id => $filter ) {

			if ( ! isset( $filter['callback'] ) ) {
				continue;
			}

			// Test that the filter has not yet been added.
			$this->assertFalse( has_filter( $filter['hook'], $filter['callback'] ) );

			// Let's add it.
			$this->assertTrue( beans_add_filter( $filter['hook'], $filter['callback'], $filter['priority'], $filter['args'] ) );

			// Check that the filter was registered.
			$this->assertTrue( has_filter( $filter['hook'], $filter['callback'] ) !== false );
		}
	}

	/**
	 * Test beans_add_filter() should add (register) the "anonymous" filter.
	 *
	 * Note: When the callback is not callable, Beans creates an anonymous filter, where the "callback" parameter in
	 * the filter is actually the "value" that will be returned when the filter fires.
	 */
	public function test_should_add_anonymous_filter() {

		foreach ( static::$test_filters as $beans_id => $filter ) {

			if ( ! isset( $filter['value_to_return'] ) ) {
				continue;
			}

			// Let's add it.
			$object = beans_add_filter( $filter['hook'], $filter['value_to_return'], $filter['priority'], $filter['args'] );

			// Check that the value stored in the anonymous callback matches our filter.
			$this->assertSame( $filter['value_to_return'], $object->value_to_return );

			// Check that the filter was registered.
			$this->assertTrue( has_filter( $filter['hook'], array( $object, 'callback' ) ) !== false );

			// Clean up.
			remove_filter( $filter['hook'], array( $object, 'callback' ), $filter['priority'] );
		}
	}
}
