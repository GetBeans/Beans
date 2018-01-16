<?php
/**
 * Tests for beans_remove_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansRemoveAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansRemoveAction extends Actions_Test_Case {

	/**
	 * Test beans_remove_action() should store the "removed" action when the original action
	 * is not registered.
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

			// Remove the action. Test that an empty action is returned.
			$this->assertSame( $empty_action, beans_remove_action( $beans_id ) );

			// Check that the "empty" action is registered as "removed".
			$this->assertSame( $empty_action, _beans_get_action( $beans_id, 'removed' ) );

			// Check that the original action is not registered in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_remove_action() should store the "removed" action before the original action is "added".
	 * Once the original action is registered, then it should be removed.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_remove_action() {

		// Remove the actions.
		foreach ( static::$test_actions as $beans_id => $action ) {
			beans_remove_action( $beans_id );
		}

		// Load the post, which runs beans_add_action for each of our test actions.
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check that the action is registered as "added".
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );

			// Check that the action is not registered in WordPress.
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

			// Remove it.
			$this->assertSame( $action, beans_remove_action( $beans_id ) );

			// Check that the action is registered as "removed".
			$this->assertSame( $action, _beans_get_action( $beans_id, 'removed' ) );

			// Check that the action is no longer registered with WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}
}
