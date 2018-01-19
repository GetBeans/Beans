<?php
/**
 * Tests for _beans_add_anonymous_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Brain\Monkey\Functions;
use WP_UnitTestCase;

/**
 * Class Tests_BeansAddAnonymousAction
 *
 * @package Beans\Framework\Integration\Unit\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansAddAnonymousAction extends WP_UnitTestCase {

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
		_beans_add_anonymous_action( 'beans_test_do_foo', array( 'foo_test_callback', array( 'foo' ) ) );

		Functions\when( 'foo_test_callback' )
			->justReturn( 'foo' );

		ob_start();
		do_action( 'beans_test_do_foo' );
		$this->assertEquals( 'foo', ob_get_clean() );
	}
}
