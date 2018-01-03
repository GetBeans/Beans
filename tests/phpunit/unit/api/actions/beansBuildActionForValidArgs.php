<?php
/**
 * Tests for _beans_build_action_for_valid_args()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansBuildActionForValidArgs
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansBuildActionForValidArgs extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
	}

	/**
	 * Test _beans_build_action_for_valid_args() should return empty array when all of the arguments are invalid.
	 */
	public function test_should_return_empty_array_when_invalid_arguments() {
		$this->assertEmpty( _beans_build_action_for_valid_args() );
		$this->assertEmpty( _beans_build_action_for_valid_args( '', '' ) );
		$this->assertEmpty( _beans_build_action_for_valid_args( null, false, '', array( 1 ) ) );
	}

	/**
	 * Test _beans_build_action_for_valid_args() should return only the "hook" parameter.
	 */
	public function test_should_return_only_hook() {
		$hooks = array( 'foo', 'bar', 'baz', 'beans' );
		foreach ( $hooks as $hook ) {
			$this->assertEquals( array( 'hook' => $hook ), _beans_build_action_for_valid_args( $hook ) );
		}
	}

	/**
	 * Test _beans_build_action_for_valid_args() should return only the "callback" parameter.
	 */
	public function test_should_return_only_callback() {
		$callbacks = array( 'foo_callback', 'my_callback', 'Foo::cb', array( $this, __FUNCTION__ ) );
		foreach ( $callbacks as $callback ) {
			$this->assertEquals(
				array( 'callback' => $callback ),
				_beans_build_action_for_valid_args( null, $callback )
			);
		}
	}

	/**
	 * Test _beans_build_action_for_valid_args() should return only the "priority" parameter.
	 */
	public function test_should_return_only_priority() {
		$priorities = array( 10, 0, 50, '20' );
		foreach ( $priorities as $priority ) {
			$this->assertEquals(
				array( 'priority' => (int) $priority ),
				_beans_build_action_for_valid_args( null, null, $priority )
			);
		}
	}

	/**
	 * Test _beans_build_action_for_valid_args() should return only the "args" parameter.
	 */
	public function test_should_return_only_args() {

		foreach ( array( 0, 1, 2, '3', '4.1' ) as $args ) {
			$this->assertEquals(
				array( 'args' => (int) $args ),
				_beans_build_action_for_valid_args( null, null, null, $args )
			);
		}
	}

	/**
	 * Test _beans_build_action_for_valid_args() should return only the valid arguments.
	 */
	public function test_should_return_valid_args() {
		$this->assertEquals(
			array(
				'hook'     => 'foo',
				'callback' => 'cb',
			),
			_beans_build_action_for_valid_args( 'foo', 'cb', '', false )
		);

		$this->assertEquals(
			array(
				'hook'     => 'foo',
				'callback' => 'cb',
				'args'     => 1,
			),
			_beans_build_action_for_valid_args( 'foo', 'cb', '', 1 )
		);

		$this->assertEquals(
			array(
				'hook'     => 'foo',
				'priority' => 50,
			),
			_beans_build_action_for_valid_args( 'foo', '', '50' )
		);

		$this->assertEquals(
			array(
				'hook'     => 'foo',
				'callback' => 'my_callback',
				'priority' => 0,
				'args'     => 0,
			),
			_beans_build_action_for_valid_args( 'foo', 'my_callback', 0, '0.0' )
		);

		$this->assertEquals(
			array(
				'hook'     => 'baz',
				'callback' => 'baz_cb',
				'priority' => 20,
				'args'     => 2,
			),
			_beans_build_action_for_valid_args( 'baz', 'baz_cb', 20, 2 )
		);
	}
}
