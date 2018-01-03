<?php
/**
 * Tests for _beans_get_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansGetAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGetAction extends Test_Case {

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
	 * Test _beans_get_action() should return false when the status is empty, i.e. no actions are registered.
	 */
	public function test_should_return_false_when_status_is_empty() {
		foreach ( $this->action_status as $action_status ) {
			$this->assertFalse( _beans_get_action( 'foo', $action_status ) );
		}
	}

	/**
	 * Test _beans_get_action() should return false when the action is not registered.
	 */
	public function test_should_return_false_when_action_is_not_registered() {
		global $_beans_registered_actions;
		$_beans_registered_actions['added']['foo'] = $this->encoded_action;

		$this->assertFalse( _beans_get_action( 'foobar', 'added' ) );

		// Make sure we get false on the other statuses.
		foreach ( $this->action_status as $action_status ) {

			// Skip the 'added' status.
			if ( 'added' === $action_status ) {
				continue;
			}

			$this->assertFalse( _beans_get_action( 'foo', $action_status ) );
		}
	}

	/**
	 * Test _beans_get_action() should return the action's configuration when it's registered.
	 */
	public function test_should_return_action_when_registered() {
		global $_beans_registered_actions;

		foreach ( $this->action_status as $action_status ) {
			$_beans_registered_actions[ $action_status ]['foo'] = $this->encoded_action;
			$this->assertEquals( $this->action, _beans_get_action( 'foo', $action_status ) );
		}
	}
}
