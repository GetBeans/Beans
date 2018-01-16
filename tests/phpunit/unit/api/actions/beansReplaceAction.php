<?php
/**
 * Tests for beans_replace_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Replace_Action_Test_Case;

require_once __DIR__ . '/includes/class-replace-action-test-case.php';

/**
 * Class Tests_BeansReplaceAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansReplaceAction extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action() should store the "replaced" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$replaced_action = array(
			'callback' => 'my_new_callback',
		);

		foreach ( static::$test_ids as $beans_id ) {
			// Test that the original action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );

			// Now store away the "replace" hook.
			$this->assertFalse( beans_replace_action( $beans_id, null, $replaced_action['callback'] ) );

			// Check that it was stored as "modified" and "added".
			$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_replace_action() should store the "replaced" action when the original action
	 * has not yet been registered.  Once the original action is registered, then it should be replaced.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_replace_action() {
		$replaced_action = array(
			'callback' => 'my_new_callback',
		);

		// Now replace the actions.
		foreach ( static::$test_ids as $beans_id ) {
			beans_replace_action( $beans_id, null, $replaced_action['callback'] );
		}

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check if it replaced the callback.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $replaced_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
			$this->check_registered_in_wp( $original_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_replace_action() should return false when there's nothing to replace,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_replace() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$this->assertFalse( beans_replace_action( $beans_id ) );

			// Verify that it did not get stored in "replaced" or "modified".
			global $_beans_registered_actions;
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['replaced'] );
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action has not been replaced.
			$this->assertSame( $action_config, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's hook.
	 */
	public function test_should_replace_the_action_hook() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$original_action = _beans_get_action( $beans_id, 'added' );

			// Make sure the callback is what we think before we get rolling.
			$this->assertEquals( $action_config['hook'], $original_action['hook'] );

			// Set up what will get stored in Beans.
			$replaced_action = array(
				'hook' => 'foo',
			);

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, $replaced_action['hook'] ) );

			// Check if it replaced only the hook.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $replaced_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
			$this->check_registered_in_wp( $new_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's callback.
	 */
	public function test_should_replace_the_action_callback() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$original_action = _beans_get_action( $beans_id, 'added' );

			// Make sure the callback is what we think before we get rolling.
			$this->assertEquals( $action_config['callback'], $original_action['callback'] );

			// Set up what will get stored in Beans.
			$replaced_action = array(
				'callback' => 'foo',
			);

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, $replaced_action['callback'] ) );

			// Check if it replaced only the callback.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $replaced_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
			$this->check_registered_in_wp( $original_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's priority level.
	 */
	public function test_should_replace_the_action_priority() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$original_action = _beans_get_action( $beans_id, 'added' );

			// Make sure the priority is what we think before we get rolling.
			$this->assertEquals( $action_config['priority'], $original_action['priority'] );

			// Set up what will get stored in Beans.
			$replaced_action = array(
				'priority' => 50,
			);

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, null, $replaced_action['priority'] ) );

			// Check if it replaced only the priority.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $replaced_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
			$this->check_registered_in_wp( $original_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's number of arguments.
	 */
	public function test_should_replace_the_action_args() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$original_action = _beans_get_action( $beans_id, 'added' );

			// Make sure the args are what we think before we get rolling.
			$this->assertEquals( $action_config['args'], $original_action['args'] );

			// Set up what will get stored in Beans.
			$replaced_action = array(
				'args' => 5,
			);

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, null, null, $replaced_action['args'] ) );

			// Check if it replaced only the args.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $replaced_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
			$this->check_registered_in_wp( $original_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_replace_action() should replace the original registered action.
	 */
	public function test_should_replace_the_action() {
		// Set up what will get stored in Beans.
		$replaced_action = array(
			'hook'     => 'new_hook',
			'callback' => 'new_callback',
			'priority' => 99,
			'args'     => 10,
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Make sure the action is what we think before we get rolling.
			$this->assertEquals( $original_action, _beans_get_action( $beans_id, 'added' ) );

			// Run the replace.
			$this->assertTrue( beans_replace_action(
				$beans_id,
				$replaced_action['hook'],
				$replaced_action['callback'],
				$replaced_action['priority'],
				$replaced_action['args']
			) );

			// Check that the "replaced" action has been stored in Beans.
			$this->assertSame( $replaced_action, _beans_get_action( $beans_id, 'added' ) );
			$this->assertSame( $replaced_action, _beans_get_action( $beans_id, 'replaced' ) );
			$this->assertSame( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );

			// Check that the new action is now registered in WordPress.
			$this->check_registered_in_wp( $replaced_action['hook'], $replaced_action );
		}
	}
}
