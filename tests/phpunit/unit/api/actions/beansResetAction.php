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
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Remove the action.
			beans_remove_action( $beans_id );

			// Check that the action was removed.
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

			// Modify the hook.
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
		$modified_action = array(
			'callback' => 'my_callback',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Before we start, check that the action is registered.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

			// Modify the callback.
			beans_modify_action_callback( $beans_id, $modified_action['callback'] );

			// Check that the action's callback was modified.
			$this->assertSame( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

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
		$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );
		$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );

		$container = Monkey\Container::instance();
		$this->assertTrue(
			$container->hookStorage()->isHookAdded(
				Monkey\Hook\HookStorage::ACTIONS,
				$action['hook'],
				$action['callback']
			)
		);
	}
}
