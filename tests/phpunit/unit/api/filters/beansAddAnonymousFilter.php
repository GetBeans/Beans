<?php
/**
 * Tests for _beans_add_anonymous_filter()
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Filters;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey\Filters;

/**
 * Class Tests_BeansAddAnonymousFilter
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansAddAnonymousFilter extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		if ( ! defined( 'BEANS_API_PATH' ) ) {
			define( 'BEANS_API_PATH', BEANS_TESTS_LIB_DIR . 'api/' );
		}
		require_once BEANS_TESTS_LIB_DIR . 'api/filters/functions.php';
	}

	/**
	 * Test _beans_add_anonymous_filter() should store callback.
	 */
	public function test_should_store_callback() {
		$object = _beans_add_anonymous_filter( 'do_foo', 'foo', 20 );

		$this->assertSame( 'foo', $object->value_to_return );

		// Clean up.
		remove_action( 'do_foo', array( $object, 'callback' ), 20 );
	}

	/**
	 * Test _beans_add_anonymous_filter() should register callback to the given hook.
	 */
	public function test_should_register_callback_to_hook() {
		$object = _beans_add_anonymous_filter( 'do_foo', false, 20 );

		$this->assertTrue( has_filter( 'do_foo', array( $object, 'callback' ) ) !== false );

		// Clean up.
		remove_action( 'do_foo', array( $object, 'callback' ), 20 );
	}

	/**
	 * Test _beans_add_anonymous_filter() should call callback on the given hook.
	 */
	public function test_should_call_callback() {

		foreach ( [ false, 'beans', 19, [ 'foo' ] ] as $value ) {
			$object = _beans_add_anonymous_filter( 'beans_test_do_foo', $value, 20 );

			Filters\expectApplied( 'beans_test_do_foo' )
				->once()
				->andReturnUsing( function( $arg ) use ( $object, $value ) {
					$this->assertSame( 'foo', $arg );
					$this->assertSame( $value, $object->callback() );
					return $object->callback();
				} );

			$this->assertSame( $value, apply_filters( 'beans_test_do_foo', 'foo' ) );

			// Clean up.
			remove_action( 'beans_test_do_foo', array( $object, 'callback' ), 20 );
		}
	}
}
