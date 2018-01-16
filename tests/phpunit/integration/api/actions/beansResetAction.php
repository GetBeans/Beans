<?php
/**
 * Tests for beans_reset_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansResetAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
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
	 * Test beans_reset_action() should reset the original action after it was "removed" via beans_remove_action().
	 */
	public function test_should_reset_after_remove() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Remove it.
			beans_remove_action( $beans_id );

			// Check that the action did get removed.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'removed' ) );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'removed' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's hook after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_hook() {
		$modified_action = array(
			'hook' => 'foo',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Now modify the hook.
			beans_modify_action_hook( $beans_id, $modified_action['hook'] );

			// Check that the hook was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertTrue( has_action( $modified_action['hook'], $action['callback'] ) !== false );

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_callback() {
		global $wp_filter;

		$modified_action = array(
			'callback' => 'my_callback',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Grab what's registered in WordPress.
			$action_in_wp = $wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ];

			// Modify the callback.
			beans_modify_action_callback( $beans_id, $modified_action['callback'] );

			// Check that the action's callback was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals( $action['callback'], $action_in_wp[ $action['callback'] ]['function'] );

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's priority after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_priority() {
		global $wp_filter;

		$modified_action = array(
			'priority' => 9999,
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, do a hard check to ensure the original priority is registered.
			$this->assertEquals(
				$action['callback'],
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]['function']
			);

			// Modify the priority.
			beans_modify_action_priority( $beans_id, $modified_action['priority'] );

			// Check that the priority was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals(
				$action['callback'],
				$wp_filter[ $action['hook'] ]->callbacks[ $modified_action['priority'] ][ $action['callback'] ]['function']
			);

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's args after it was "modified".
	 */
	public function test_should_reset_after_modifying_the_args() {
		global $wp_filter;

		$modified_action = array(
			'args' => 14,
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, do a hard check to ensure the original number of args is registered.
			$this->assertEquals(
				$action['args'],
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]['accepted_args']
			);

			// Modify the number of arguments.
			beans_modify_action_arguments( $beans_id, $modified_action['args'] );

			// Check that the number of arguments was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals(
				$modified_action['args'],
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]['accepted_args']
			);

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback and args after they were "modified".
	 */
	public function test_should_reset_after_modifying_callback_and_args() {
		global $wp_filter;

		$this->go_to_post();

		$modified_action = array(
			'callback' => 'my_callback',
			'args'     => 14,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->check_the_action( $beans_id, $action );

			// Modify the action.
			beans_modify_action( $beans_id, null, $modified_action['callback'], null, $modified_action['args'] );

			// Check that the action was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertTrue( has_action( $action['hook'], $modified_action['callback'] ) !== false );
			$this->assertEquals(
				array(
					'function'      => $modified_action['callback'],
					'accepted_args' => $modified_action['args'],
				),
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $modified_action['callback'] ]
			);

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's priority and args after they were "modified".
	 */
	public function test_should_reset_after_modifying_priority_and_args() {
		global $wp_filter;

		$this->go_to_post();

		$modified_action = array(
			'priority' => 79,
			'args'     => 14,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->check_the_action( $beans_id, $action );

			// Modify the action.
			beans_modify_action( $beans_id, null, null, $modified_action['priority'], $modified_action['args'] );

			// Check that the action was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals(
				array(
					'function'      => $action['callback'],
					'accepted_args' => $modified_action['args'],
				),
				$wp_filter[ $action['hook'] ]->callbacks[ $modified_action['priority'] ][ $action['callback'] ]
			);

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback and priority after they were "modified".
	 */
	public function test_should_reset_after_modifying_callback_and_priority() {
		global $wp_filter;

		$this->go_to_post();

		$modified_action = array(
			'callback' => 'foo',
			'priority' => 39,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->check_the_action( $beans_id, $action );

			// Modify the action.
			beans_modify_action( $beans_id, null, $modified_action['callback'], $modified_action['priority'] );

			// Check that the action was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals(
				array(
					'function'      => $modified_action['callback'],
					'accepted_args' => $action['args'],
				),
				$wp_filter[ $action['hook'] ]->callbacks[ $modified_action['priority'] ][ $modified_action['callback'] ]
			);

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action's callback, priority, and args after they were
	 * "modified".
	 */
	public function test_should_reset_after_modifying_all_but_hook() {
		global $wp_filter;

		$this->go_to_post();

		$modified_action = array(
			'callback' => 'foo',
			'priority' => 39,
			'args'     => 24,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->check_the_action( $beans_id, $action );

			// Modify the action.
			beans_modify_action( $beans_id, null, $modified_action['callback'], $modified_action['priority'], $modified_action['args'] );

			// Check that the action was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
			$this->assertEquals(
				array(
					'function'      => $modified_action['callback'],
					'accepted_args' => $modified_action['args'],
				),
				$wp_filter[ $action['hook'] ]->callbacks[ $modified_action['priority'] ][ $modified_action['callback'] ]
			);

			// Let's reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action was reset.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->check_the_action( $beans_id, $action );
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
	 * Test beans_reset_action() should not reset after replacing the action's priority.
	 *
	 * Why? "Replace" overwrites and is not resettable.
	 */
	public function test_should_not_reset_after_replacing_priority() {
		global $wp_filter;

		$priority = 17;

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, do a hard check to ensure the original priority is registered.
			$this->assertEquals(
				array(
					'function'      => $action['callback'],
					'accepted_args' => $action['args'],
				),
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]
			);

			// Run the replace.
			beans_replace_action_priority( $beans_id, $priority );

			// Let's try to reset the action.
			$this->assertSame( $priority, beans_reset_action( $beans_id )['priority'] );

			// Check that the action was not reset.
			$this->assertEquals(
				array(
					'function'      => $action['callback'],
					'accepted_args' => $action['args'],
				),
				$wp_filter[ $action['hook'] ]->callbacks[ $priority ][ $action['callback'] ]
			);
		}
	}

	/**
	 * Test beans_reset_action() should not reset after replacing the action's args.
	 *
	 * Why? "Replace" overwrites and is not resettable.
	 */
	public function test_should_not_reset_after_replacing_args() {
		global $wp_filter;

		$args = 29;

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, do a hard check to ensure the original number of args is registered.
			$this->assertEquals(
				$action['args'],
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]['accepted_args']
			);

			// Run the replace.
			beans_replace_action_arguments( $beans_id, $args );

			// Let's try to reset the action.
			$this->assertSame( $args, beans_reset_action( $beans_id )['args'] );

			// Check that the action was not reset.
			$this->assertEquals(
				$args,
				$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]['accepted_args']
			);
		}
	}

	/**
	 * Check that the action was reset.
	 *
	 * @since 1.5.0
	 *
	 * @param string $beans_id The action's Beans ID, a unique ID tracked within Beans for this action.
	 * @param array  $action   The action to check.
	 *
	 * @return void
	 */
	protected function check_the_action( $beans_id, array $action ) {
		global $wp_filter;

		$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );
		$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

		$this->assertEquals(
			array(
				'function'      => $action['callback'],
				'accepted_args' => $action['args'],
			),
			$wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ][ $action['callback'] ]
		);
	}
}
