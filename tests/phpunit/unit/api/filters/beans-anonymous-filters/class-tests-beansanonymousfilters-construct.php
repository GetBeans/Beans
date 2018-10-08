<?php
/**
 * Tests for the __construct() method of Beans_Anonymous_Filters.
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Filters;

use Beans_Anonymous_Filters;
use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansAnonymousFilters_Construct.
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 * @group   api
 * @group   api-filters
 */
class Tests_BeansAnonymousFilters_Construct extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/filters/class-beans-anonymous-filters.php';
	}

	/**
	 * Test __construct() should store callback.
	 */
	public function test_should_store_callback() {
		$object = new Beans_Anonymous_Filters( 'do_foo', 'foo', 20 );

		$this->assertSame( 'foo', $object->value_to_return );

		// Clean up.
		remove_action( 'do_foo', [ $object, 'callback' ], 20 );
	}

	/**
	 * Test __construct() should register callback to the given hook.
	 */
	public function test_should_register_callback_to_hook() {
		$object = new Beans_Anonymous_Filters( 'do_foo', false, 20 );

		$this->assertTrue( has_filter( 'do_foo', [ $object, 'callback' ] ) !== false );

		// Clean up.
		remove_action( 'do_foo', [ $object, 'callback' ], 20 );
	}
}
