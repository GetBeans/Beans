<?php
/**
 * Tests for beans_remove_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Replace_Action_Test_Case;

require_once __DIR__ . '/includes/class-replace-action-test-case.php';

/**
 * Class Tests_BeansRemoveAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansRemoveAction extends Replace_Action_Test_Case {

	/**
	 * Test beans_remove_action() should store the "removed" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$empty_action = array(
			'hook'     => null,
			'callback' => null,
			'priority' => null,
			'args'     => null,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Test that the original action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );
			$this->assertFalse( _beans_get_current_action( $beans_id ) );

			// Now do the remove and test that it returns on empty action.
			$this->assertSame( $empty_action, beans_remove_action( $beans_id ) );

			// Check that stored.
			$this->assertSame( $empty_action, _beans_get_action( $beans_id, 'removed' ) );

			// Check that it is not registered in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_remove_action() should store the "removed" action when the original action
	 * has not yet been registered.  Once the original action is registered, then it should be removed.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_replace_action() {

		// Remove the actions.
		foreach ( static::$test_actions as $beans_id => $action ) {
			beans_remove_action( $beans_id );
		}

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check if it the action is stored as "added".
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );

			// Check that it is not registered in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_remove_action() should remove a registered action.
	 */
	public function test_should_remove_a_registered_action() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check that the action is registered with Beans.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );

			// Check that the action is registered with WordPress.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Do the remove.
			$this->assertSame( $action, beans_remove_action( $beans_id ) );

			// Check what is stored in "removed".
			$this->assertSame( $action, _beans_get_action( $beans_id, 'removed' ) );

			// Check that is no longer registered with WordPress, i.e. that `remove_action` did run.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}
}
