<?php
/**
 * Tests for _beans_set_action().
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansSetAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansSetAction extends Actions_Test_Case {

	/**
	 * The registered actions' status.
	 *
	 * @var array
	 */
	protected $statuses = [ 'added', 'modified', 'removed', 'replaced' ];

	/**
	 * Test _beans_set_action() should set (registered) the action and then return it.
	 */
	public function test_should_set_and_return_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {

			// Test each status.
			foreach ( $this->statuses as $status ) {
				// Before we start, check that the action is not set.
				$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions[ $status ] );

				// Now do the tests.
				$this->assertSame( $action, _beans_set_action( $beans_id, $action, $status ) );
				$this->assertArrayHasKey( $beans_id, $_beans_registered_actions[ $status ] );
				$this->assertSame( $action, $_beans_registered_actions[ $status ][ $beans_id ] );
			}
		}
	}

	/**
	 * Test _beans_set_action() should not overwrite an existing registered action.
	 */
	public function test_should_not_overwrite_existing_registered_action() {
		global $_beans_registered_actions;

		$new_action = [
			'hook'     => 'bar',
			'callback' => 'callback_bar',
			'priority' => 20,
			'args'     => 2,
		];

		foreach ( static::$test_actions as $beans_id => $action ) {

			// Test each status.
			foreach ( $this->statuses as $status ) {
				// Register the original action.
				$this->assertSame( $action, _beans_set_action( $beans_id, $action, $status ) );

				// Now test that it does not overwrite the previously registered action.
				$this->assertSame( $action, _beans_set_action( $beans_id, $new_action, $status ) );
				$this->assertSame( $action, $_beans_registered_actions[ $status ][ $beans_id ] );
			}
		}
	}

	/**
	 * Test _beans_set_action() should overwrite the existing registered action when the "overwrite"
	 * argument is set to true.
	 */
	public function test_should_overwrite_existing_registered_action() {
		global $_beans_registered_actions;

		$new_action = [
			'hook'     => 'bar',
			'callback' => 'callback_bar',
			'priority' => 20,
			'args'     => 2,
		];

		foreach ( static::$test_actions as $beans_id => $action ) {

			// Test each status.
			foreach ( $this->statuses as $status ) {
				// Register the original action.
				$this->assertSame( $action, _beans_set_action( $beans_id, $action, $status ) );
				$this->assertSame( $action, $_beans_registered_actions[ $status ][ $beans_id ] );

				// Now test that it does overwrite the previously registered action.
				$this->assertSame( $new_action, _beans_set_action( $beans_id, $new_action, $status, true ) );
				$this->assertSame( $new_action, $_beans_registered_actions[ $status ][ $beans_id ] );
			}
		}
	}
}
