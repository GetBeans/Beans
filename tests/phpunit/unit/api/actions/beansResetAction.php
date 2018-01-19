<?php
/**
 * Tests for beans_reset_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansResetAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansResetAction extends Actions_Test_Case {

	/**
	 * Test beans_reset_action() should return false when the action is not registered.
	 */
	public function test_should_return_false_when_no_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			$this->assertFalse( beans_reset_action( $beans_id ) );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action after it was "removed".
	 */
	public function test_should_reset_after_remove() {
		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Remove the action.
			beans_remove_action( $beans_id );

			// Check that the action was removed in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );

			// Check that the action was "removed" in Beans.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'removed' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action, true );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's hook after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_hook() {
		$modified_action = array(
			'hook' => 'foo',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $modified_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action ) {
					// Check that the parameters remain unchanged in WordPress.
					$this->assertSame( $action['callback'], $callback );
					$this->assertSame( $action['priority'], $priority );
					$this->assertSame( $action['args'], $args );
				} );

			// Modify the action's hook.
			beans_modify_action_hook( $beans_id, $modified_action['hook'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_callback() {
		$modified_action = array(
			'callback' => 'my_callback',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that the callback was modified in WordPress.
					$this->assertSame( $modified_action['callback'], $callback );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $action['priority'], $priority );
					$this->assertSame( $action['args'], $args );
				} );

			// Modify the action's callback.
			beans_modify_action_callback( $beans_id, $modified_action['callback'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's priority after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_priority() {
		$modified_action = array(
			'priority' => 9999,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that the priority was modified in WordPress.
					$this->assertSame( $modified_action['priority'], $priority );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $action['callback'], $callback );
					$this->assertSame( $action['args'], $args );
				} );

			// Modify the action's priority.
			beans_modify_action_priority( $beans_id, $modified_action['priority'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's args after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_args() {
		$modified_action = array(
			'args' => 14,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that the args was modified in WordPress.
					$this->assertSame( $modified_action['args'], $args );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $action['callback'], $callback );
					$this->assertSame( $action['priority'], $priority );
				} );

			// Modify the action's args.
			beans_modify_action_arguments( $beans_id, $modified_action['args'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback and args after they were "modified".
	 */
	public function test_should_reset_after_modifying_callback_and_args() {
		$modified_action = array(
			'callback' => 'my_callback',
			'args'     => 14,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that the callback and args were modified in WordPress.
					$this->assertSame( $modified_action['callback'], $callback );
					$this->assertSame( $modified_action['args'], $args );
					// Check that the priority remains unchanged.
					$this->assertSame( $action['priority'], $priority );
				} );

			// Modify the action's callback and args.
			beans_modify_action( $beans_id, null, $modified_action['callback'], null, $modified_action['args'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's priority and args after they were "modified".
	 */
	public function test_should_reset_after_modifying_priority_and_args() {
		$modified_action = array(
			'priority' => 79,
			'args'     => 14,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that the priority and args were modified in WordPress.
					$this->assertSame( $modified_action['priority'], $priority );
					$this->assertSame( $modified_action['args'], $args );
					// Check that the callback remains unchanged.
					$this->assertSame( $action['callback'], $callback );
				} );

			// Modify the action's priority and args.
			beans_modify_action( $beans_id, null, null, $modified_action['priority'], $modified_action['args'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback and priority after they were "modified".
	 */
	public function test_should_reset_after_modifying_callback_and_priority() {
		$modified_action = array(
			'callback' => 'foo',
			'priority' => 39,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that the callback and priority were modified in WordPress.
					$this->assertSame( $modified_action['callback'], $callback );
					$this->assertSame( $modified_action['priority'], $priority );
					// Check that the args remains unchanged.
					$this->assertSame( $action['args'], $args );
				} );

			// Modify the action's callback and priority.
			beans_modify_action( $beans_id, null, $modified_action['callback'], $modified_action['priority'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback, priority, and args after they were
	 * "modified".
	 */
	public function test_should_reset_after_modifying_all_but_hook() {
		$modified_action = array(
			'callback' => 'foo',
			'priority' => 39,
			'args'     => 24,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $action, $modified_action ) {
					// Check that all of the parameters were modified in WordPress.
					$this->assertSame( $modified_action['callback'], $callback );
					$this->assertSame( $modified_action['priority'], $priority );
					$this->assertSame( $modified_action['args'], $args );
				} );

			// Modify the action.
			beans_modify_action( $beans_id, null, $modified_action['callback'], $modified_action['priority'], $modified_action['args'] );

			// Check that the action was modified in Beans.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Reset and then check in WordPress and Beans.
			$this->reset_and_check( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should not reset after replacing the action's hook.
	 *
	 * Why? "Replace" overwrites and is not resettable.
	 */
	public function test_should_not_reset_after_replacing_hook() {
		$hook = 'foo';

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Run the replace.
			beans_replace_action_hook( $beans_id, $hook );

			// Let's try to reset the action.
			$this->assertSame( $hook, beans_reset_action( $beans_id )['hook'] );

			// Check that the action was not reset.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertTrue( has_action( $hook, $action['callback'] ) !== false );
		}
	}

	/**
	 * Test beans_reset_action() should not reset after replacing the action's callback.
	 *
	 * Why? "Replace" overwrites and is not resettable.
	 */
	public function test_should_not_reset_after_replacing_callback() {
		$callback = 'foo_cb';

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Run the replace.
			beans_replace_action_callback( $beans_id, $callback );

			// Let's try to reset the action.
			$this->assertSame( $callback, beans_reset_action( $beans_id )['callback'] );

			// Check that the action was not reset.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertTrue( has_action( $action['hook'], $callback ) !== false );
		}
	}

	/**
	 * Reset the action.
	 *
	 * 1. Set up the WordPress simulator before we reset the action.
	 * 2. Then reset the action.
	 * 3. Check that the action was reset in WordPress.
	 * 4. Check that the action was reset in Beans.
	 *
	 * @since 1.5.0
	 *
	 * @param string $beans_id        Beans' action ID.
	 * @param array  $original_action Action's original configuration.
	 * @param bool   $after_remove    Set to true when resetting after a remove.
	 *
	 * @return void
	 * @throws Monkey\Expectation\Exception\NotAllowedMethod Throws when callback is invalid.
	 */
	protected function reset_and_check( $beans_id, $original_action, $after_remove = false ) {
		Monkey\Actions\expectAdded( $original_action['hook'] )
			->once()
			->whenHappen( function( $callback, $priority, $args ) use ( $original_action ) {
				$this->assertSame( $original_action['args'], $args );
				$this->assertSame( $original_action['callback'], $callback );
				$this->assertSame( $original_action['priority'], $priority );
			} );

		$this->assertSame( $original_action, beans_reset_action( $beans_id ) );

		if ( $after_remove ) {
			$this->assertFalse( _beans_get_action( $beans_id, 'removed' ) );
		} else {
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
		}

		$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );
		$this->assertTrue( has_action( $original_action['hook'], $original_action['callback'] ) !== false );
	}
}
