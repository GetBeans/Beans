<?php
/**
 * Tests for _beans_set_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansSetAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansSetAction extends Test_Case {

	/**
	 * Available action statuses.
	 *
	 * @var array
	 */
	protected $action_status;

	/**
	 * Action to be/is registered.
	 *
	 * @var array
	 */
	protected $action;

	/**
	 * The json_encode() version of the above action.
	 *
	 * @var string
	 */
	protected $encoded_action;

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->action_status = array( 'added', 'modified', 'removed', 'replaced' );
		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';

		$this->action         = array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		);
		$this->encoded_action = wp_json_encode( $this->action );
	}

	/**
	 * Reset the test fixture.
	 */
	protected function tearDown() {
		parent::tearDown();

		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);
	}

	/**
	 * Test _beans_set_action() should set the action into the registered actions and then return it.
	 */
	public function test_should_set_and_return_action() {
		global $_beans_registered_actions;

		foreach ( $this->action_status as $action_status ) {
			// Check that it's not set before we start.
			$this->assertFalse( isset( $_beans_registered_actions[ $action_status ]['foo'] ) );

			// Now test that it does properly set the action for the given status.
			$this->assertEquals( $this->action, _beans_set_action( 'foo', $this->action, $action_status ) );
			$this->assertArrayHasKey( 'foo', $_beans_registered_actions[ $action_status ] );
			$this->assertEquals( $this->encoded_action, $_beans_registered_actions[ $action_status ]['foo'] );
		}
	}

	/**
	 * Test _beans_set_action() should not overwrite an existing registered action.
	 */
	public function test_should_not_overwrite_existing_registered_action() {
		global $_beans_registered_actions;

		$new_action = array(
			'hook'     => 'bar',
			'callback' => 'callback_bar',
			'priority' => 20,
			'args'     => 2,
		);

		foreach ( $this->action_status as $action_status ) {

			// Register the original one first.
			_beans_set_action( 'foo', $this->action, $action_status );

			// Now test that it does not overwrite the previously registered action.
			$this->assertEquals( $this->action, _beans_set_action( 'foo', $new_action, $action_status ) );
			$this->assertEquals( $this->encoded_action, $_beans_registered_actions[ $action_status ]['foo'] );
		}
	}

	/**
	 * Test _beans_set_action() should overwrite an existing registered action.
	 */
	public function test_should_overwrite_existing_registered_action() {
		global $_beans_registered_actions;

		$new_action         = array(
			'hook'     => 'bar',
			'callback' => 'callback_bar',
			'priority' => 20,
			'args'     => 2,
		);
		$encoded_new_action = wp_json_encode( $new_action );

		foreach ( $this->action_status as $action_status ) {

			// Register the original one first.
			_beans_set_action( 'foo', $this->action, $action_status );

			// Now test that it does overwrite the previously registered action.
			$this->assertEquals( $new_action, _beans_set_action( 'foo', $new_action, $action_status, true ) );
			$this->assertEquals( $encoded_new_action, $_beans_registered_actions[ $action_status ]['foo'] );
		}
	}
}
