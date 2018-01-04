<?php
/**
 * Tests for _beans_merge_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansMergeAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansMergeAction extends Test_Case {

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
	 * Test _beans_set_action() should merge the new action's configuration with the registered one and then return it.
	 */
	public function test_should_merge_and_return() {
		global $_beans_registered_actions;

		$expected             = $this->action;
		$expected['priority'] = 20;

		foreach ( $this->action_status as $action_status ) {
			// Set the original action configuration.
			_beans_set_action( 'foo', $this->action, $action_status );

			// Now test that it does properly merge the action for the given status.
			$this->assertEquals( $expected, _beans_merge_action( 'foo', array( 'priority' => 20 ), $action_status ) );
			$this->assertEquals( wp_json_encode( $expected ), $_beans_registered_actions[ $action_status ]['foo'] );
		}
	}

	/**
	 * Test _beans_merge_action() should store new action's configuration, when it's not already registered.
	 */
	public function test_should_store_new_action() {
		global $_beans_registered_actions;

		foreach ( $this->action_status as $action_status ) {
			$this->assertEquals( $this->action, _beans_merge_action( 'foo', $this->action, $action_status ) );
			$this->assertEquals( $this->encoded_action, $_beans_registered_actions[ $action_status ]['foo'] );
		}
	}
}
