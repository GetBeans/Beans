<?php
/**
 * Tests for _beans_unset_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansUnsetAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansUnsetAction extends Actions_Test_Case {

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

		$this->action_status  = array( 'added', 'modified', 'removed', 'replaced' );
		$this->action         = array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		);
		$this->encoded_action = wp_json_encode( $this->action );
	}

	/**
	 * Test _beans_unset_action() should return false when action's configuration is not registered.
	 */
	public function test_should_return_false_when_not_registered() {

		foreach ( $this->action_status as $action_status ) {
			$this->assertFalse( _beans_unset_action( 'foo', $action_status ) );
		}
	}

	/**
	 * Test _beans_unset_action() should unset the registered action's configuration.
	 */
	public function test_should_unset_registered_action() {
		global $_beans_registered_actions;

		foreach ( $this->action_status as $action_status ) {

			// First, register the action.
			_beans_set_action( 'foo', $this->action, $action_status );
			$this->assertTrue( isset( $_beans_registered_actions[ $action_status ]['foo'] ) );

			// Then test that unset does its job.
			$this->assertTrue( _beans_unset_action( 'foo', $action_status ) );
			$this->assertFalse( isset( $_beans_registered_actions[ $action_status ]['foo'] ) );
		}
	}

	/**
	 * Test _beans_unset_action() should return false when the status is invalid.
	 */
	public function test_should_return_false_when_status_is_invalid() {
		$this->assertFalse( _beans_unset_action( 'foo', 'invalid_status' ) );
		$this->assertFalse( _beans_unset_action( 'foo', 'foo' ) );
		$this->assertFalse( _beans_unset_action( 'foo', 'not_valid_either' ) );

		// Now store some configurations and test it again.
		foreach ( $this->action_status as $action_status ) {
			_beans_set_action( 'foo', $this->action, $action_status );
		}

		$this->assertFalse( _beans_unset_action( 'foo', 'invalid_status' ) );
		$this->assertFalse( _beans_unset_action( 'foo', 'foo' ) );
		$this->assertFalse( _beans_unset_action( 'foo', 'not_valid_either' ) );
	}
}
