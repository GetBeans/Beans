<?php
/**
 * Tests for beans_modify_action()
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
 * Class Tests_BeansModifyAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyAction extends Actions_Test_Case {

	/**
	 * Test beans_modify_action() should return false when there's nothing to modify,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_modify() {
		global $_beans_registered_actions;

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			$this->assertFalse( beans_modify_action( $beans_id ) );

			// Verify that the action was not stored as "modified".
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action was not replaced.
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_modify_action() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

			// Check that it returns false.
			$this->assertFalse( beans_modify_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertEquals( $action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's hook.
	 */
	public function test_should_modify_the_action_hook() {
		$modified_action = array(
			'hook' => 'foo',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $modified_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action ) {
					// Check that the parameters remain unchanged in WordPress.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['priority'], $priority );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Modify the hook.
			$this->assertTrue( beans_modify_action( $beans_id, $modified_action['hook'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );

			// Check that the modified action was added in WordPress.
			$this->assertTrue( has_action( $modified_action['hook'], $original_action['callback'] ) !== false );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$modified_action = array(
			'callback' => 'my_callback',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_action ) {
					// Check that the callback was modified in WordPress.
					$this->assertSame( $modified_action['callback'], $callback );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['priority'], $priority );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Modify the action's callback.
			$this->assertTrue( beans_modify_action( $beans_id, null, $modified_action['callback'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );

			// Check that the modified action was added in WordPress.
			$this->assertTrue( has_action( $original_action['hook'], $modified_action['callback'] ) !== false );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's priority level.
	 */
	public function test_should_modify_the_action_priority() {
		$modified_action = array(
			'priority' => 20,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_action ) {
					// Check that the priority was modified in WordPress.
					$this->assertSame( $modified_action['priority'], $priority );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Modify the action's priority.
			$this->assertTrue( beans_modify_action( $beans_id, null, null, $modified_action['priority'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's number of arguments.
	 */
	public function test_should_modify_the_action_args() {
		$modified_action = array(
			'args' => 7,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_action ) {
					// Check that the number of arguments was modified in WordPress.
					$this->assertSame( $modified_action['args'], $args );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['priority'], $priority );
				} );

			// Modify the action's number of arguments.
			$this->assertTrue( beans_modify_action( $beans_id, null, null, null, $modified_action['args'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}
}
