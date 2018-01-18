<?php
/**
 * Tests for beans_replace_action_hook()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Replace_Action_Test_Case;

require_once __DIR__ . '/includes/class-replace-action-test-case.php';

/**
 * Class Tests_BeansReplaceActionHook
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansReplaceActionHook extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_hook() should store the "replaced" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$replaced_action = array(
			'hook' => 'my_new_hook',
		);

		foreach ( static::$test_ids as $beans_id ) {
			// Test that the original action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );

			// Now store away the "replace" hook.
			$this->assertFalse( beans_replace_action_hook( $beans_id, $replaced_action['hook'] ) );

			// Check that it was stored as "modified".
			$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}

	/**
	 * Test beans_replace_action_hook() should store the "replaced" action when the original action
	 * has not yet been registered.  Once the original action is registered, then the hook should be replaced.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_replace_the_hook() {
		$replaced_action = array(
			'hook' => 'foo',
		);

		// Now replace the actions.
		foreach ( static::$test_ids as $beans_id ) {
			beans_replace_action_hook( $beans_id, $replaced_action['hook'] );
		}

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check if it replaced the hook.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $replaced_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );
			$this->check_registered_in_wp( $replaced_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_replace_action_hook() should return false when no hook was passed.
	 */
	public function test_should_return_false_when_no_hook() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$this->assertFalse( beans_replace_action_hook( $beans_id, '' ) );

			// Verify that it did not get stored in "replaced" or "modified".
			global $_beans_registered_actions;
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['replaced'] );
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action has not been replaced.
			$this->assertSame( $action_config, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_replace_action_hook() should replace the registered action's hook.
	 */
	public function test_should_replace_the_action_hook() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			$original_action = _beans_get_action( $beans_id, 'added' );

			// Make sure the callback is what we think before we get rolling.
			$this->assertEquals( $action_config['hook'], $original_action['hook'] );

			// Set up what will get stored in Beans.
			$replaced_action = array(
				'hook' => 'beans_foo',
			);

			// Run the replace.
			$this->assertTrue( beans_replace_action_hook( $beans_id, $replaced_action['hook'] ) );

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
}
