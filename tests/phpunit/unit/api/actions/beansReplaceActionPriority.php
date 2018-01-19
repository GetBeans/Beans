<?php
/**
 * Tests for beans_replace_action_priority()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Replace_Action_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-replace-action-test-case.php';

/**
 * Class Tests_BeansReplaceActionPriority
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansReplaceActionPriority extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_priority() should store the "replaced" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$replaced_action = array(
			'priority' => 99,
		);

		foreach ( array_keys( static::$test_actions ) as $beans_id ) {
			// Test that the original action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );

			// Now store away the "replace" priority.
			$this->assertFalse( beans_replace_action_priority( $beans_id, $replaced_action['priority'] ) );

			// Check that it was stored as "modified".
			$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}

	/**
	 * Test beans_replace_action_priority() should store the "replaced" priority when the original action
	 * has not yet been registered.  Once the original action is registered, then the priority should be replaced.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_replace_the_priority() {
		$replaced_action = array(
			'priority' => 10000,
		);

		// Now replace the actions.
		foreach ( static::$test_ids as $beans_id ) {
			beans_replace_action_priority( $beans_id, $replaced_action['priority'] );
		}

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we add the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $replaced_action ) {
					// Check that the priority was replaced in WordPress.
					$this->assertSame( $replaced_action['priority'], $priority );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Now add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Check that only the priority was replaced.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $replaced_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
		}
	}

	/**
	 * Test beans_replace_action_priority() should return false when no priority is passed.
	 */
	public function test_should_return_false_when_no_priority() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			// Check it returns false.
			$this->assertFalse( beans_replace_action_priority( $beans_id, '' ) );

			// Verify that it did not get stored in "replaced" or "modified".
			global $_beans_registered_actions;
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['replaced'] );
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action has not been replaced.
			$this->assertSame( $action_config, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_replace_action_priority() should replace the registered action's priority.
	 */
	public function test_should_replace_the_action_priority() {
		$replaced_action = array(
			'priority' => 52,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we replace the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $replaced_action ) {
					// Check that the priority was replaced in WordPress.
					$this->assertSame( $replaced_action['priority'], $priority );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, null, $replaced_action['priority'] ) );

			// Check that only the priority was replaced.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $replaced_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
		}
	}
}
