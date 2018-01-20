<?php
/**
 * Tests for beans_has_filters()
 *
 * @package Beans\Framework\Tests\Integration\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Filters;

use Beans\Framework\Tests\Integration\API\Filters\Includes\Filters_Test_Case;

require_once __DIR__ . '/includes/class-filters-test-case.php';

/**
 * Class Tests_BeansHasFilters
 *
 * @package Beans\Framework\Tests\Integration\API\Filters
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansHasFilters extends Filters_Test_Case {

	/**
	 * Test beans_has_filters() should return false when no callback is registered.
	 */
	public function test_should_return_false_when_no_callback_registered() {

		foreach ( static::$test_filters as $filter ) {

			if ( ! isset( $filter['callback'] ) ) {
				continue;
			}
			$this->assertFalse( beans_has_filters( $filter['hook'], $filter['callback'] ) );
		}
	}

	/**
	 * Test beans_has_filters() should return priority number when a callback is registered.
	 */
	public function test_should_return_priority_number_when_callback_registered() {

		foreach ( static::$test_filters as $filter ) {

			if ( ! isset( $filter['callback'] ) ) {
				continue;
			}

			beans_add_filter( $filter['hook'], $filter['callback'], $filter['priority'], $filter['args'] );

			$this->assertSame( $filter['priority'], beans_has_filters( $filter['hook'], $filter['callback'] ) );

			remove_filter( $filter['hook'], $filter['callback'], $filter['priority'] );
		}
	}
}
