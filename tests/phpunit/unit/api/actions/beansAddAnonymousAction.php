<?php
/**
 * Tests for _beans_add_anonymous_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;

/**
 * Class Tests_BeansAddAnonymousAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansAddAnonymousAction extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		if ( ! defined( 'BEANS_API_PATH' ) ) {
			define( 'BEANS_API_PATH', BEANS_TESTS_LIB_DIR . 'api/' );
		}
		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
	}

	/**
	 * Test _beans_add_anonymous_action() should register callback to the given hook.
	 */
	public function test_should_register_callback_to_hook() {
		_beans_add_anonymous_action( 'do_foo', array( 'foo_test_callback', array( 'foo' ) ) );

		$this->assertTrue( has_action( 'do_foo' ) );
	}

	/**
	 * Test _beans_add_anonymous_action() should call callback on the given hook.
	 */
	public function test_should_call_callback() {
		$object = _beans_add_anonymous_action( 'beans_test_do_foo', array( 'foo_test_callback', array( 'foo' ) ) );

		Functions\when( 'foo_test_callback' )
			->justReturn( 'foo' );

		Actions\expectDone( 'beans_test_do_foo' )
			->once()
			->whenHappen( function() use ( $object ) {
				ob_start();
				$object->callback();
				$this->assertEquals( 'foo', ob_get_clean() );
			} );

		do_action( 'beans_test_do_foo' );
	}
}
