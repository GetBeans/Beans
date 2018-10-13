<?php
/**
 * Tests for beans_modify_action().
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
 * @group   api
 * @group   api-actions
 */
class Tests_BeansModifyAction extends Actions_Test_Case {

	/**
	 * Test beans_modify_action() should return false when there's nothing to modify,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_modify() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			$this->assertFalse( beans_modify_action( $beans_id ) );

			// Verify that the action was not stored as "modified".
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );
		}
	}

	/**
	 * Test beans_modify_action() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );

			// Set up the mocks.
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( false );
			Monkey\Functions\expect( '_beans_merge_action' )
				->once()
				->with( $beans_id, $action, 'modified' )
				->andReturnUsing(
					function( $id, $action ) {
						global $_beans_registered_actions;
						$_beans_registered_actions['modified'][ $id ] = $action;

						return $action;
					}
				);
			Monkey\Actions\expectAdded( $action['hook'] )->never();

			// Check that it returns false.
			$this->assertFalse( beans_modify_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertArrayHasKey( $beans_id, $_beans_registered_actions['modified'] );
			$this->assertSame( $action, $_beans_registered_actions['modified'][ $beans_id ] );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's hook.
	 */
	public function test_should_modify_the_action_hook() {
		$modified_hook = 'foo';

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( $original_action );

			// Expect that the original action gets removed.
			Monkey\Functions\expect( 'remove_action' )
				->once()
				->with( $original_action['hook'], $original_action['callback'], $original_action['priority'] );

			// When called, return the modified action.
			Monkey\Functions\expect( '_beans_merge_action' )
				->once()
				->with( $beans_id, [ 'hook' => $modified_hook ], 'modified' )
				->andReturn( [ 'hook' => $modified_hook ] );

			// Expect the modified hook is added, but not the original hook.
			Monkey\Actions\expectAdded( $original_action['hook'] )->never();
			Monkey\Actions\expectAdded( $modified_hook )
				->once()
				->with( $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Modify the hook.
			$this->assertTrue( beans_modify_action( $beans_id, $modified_hook ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$modified_callback = 'my_callback';

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( $original_action );

			// Expect that the original action gets removed.
			Monkey\Functions\expect( 'remove_action' )
				->once()
				->with( $original_action['hook'], $original_action['callback'], $original_action['priority'] );

			// When called, return the modified action.
			Monkey\Functions\expect( '_beans_merge_action' )
				->once()
				->with( $beans_id, [ 'callback' => $modified_callback ], 'modified' )
				->andReturn( [ 'callback' => $modified_callback ] );

			// Expect the original hook with the modified callback.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->with( $modified_callback, $original_action['priority'], $original_action['args'] );

			// Modify the action.
			$this->assertTrue( beans_modify_action( $beans_id, null, $modified_callback ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's priority level.
	 */
	public function test_should_modify_the_action_priority() {
		$modified_priority = 20;

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( $original_action );

			// Expect that the original action gets removed.
			Monkey\Functions\expect( 'remove_action' )
				->once()
				->with( $original_action['hook'], $original_action['callback'], $original_action['priority'] );

			// When called, return the modified action.
			Monkey\Functions\expect( '_beans_merge_action' )
				->once()
				->with( $beans_id, [ 'priority' => $modified_priority ], 'modified' )
				->andReturn( [ 'priority' => $modified_priority ] );

			// Expect the original hook with the modified callback.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->with( $original_action['callback'], $modified_priority, $original_action['args'] );

			// Modify the hook.
			$this->assertTrue( beans_modify_action( $beans_id, null, null, $modified_priority ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's number of arguments.
	 */
	public function test_should_modify_the_action_args() {
		$modified_args = 7;

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			foreach ( static::$test_actions as $beans_id => $original_action ) {
				Monkey\Functions\expect( '_beans_get_current_action' )
					->once()
					->with( $beans_id )
					->andReturn( $original_action );

				// Expect that the original action gets removed.
				Monkey\Functions\expect( 'remove_action' )
					->once()
					->with( $original_action['hook'], $original_action['callback'], $original_action['priority'] );

				// When called, return the modified action.
				Monkey\Functions\expect( '_beans_merge_action' )
					->once()
					->with( $beans_id, [ 'args' => $modified_args ], 'modified' )
					->andReturn( [ 'args' => $modified_args ] );

				// Expect the original hook with the modified callback.
				Monkey\Actions\expectAdded( $original_action['hook'] )
					->once()
					->with( $original_action['callback'], $original_action['priority'], $modified_args );

				// Modify the action.
				$this->assertTrue( beans_modify_action( $beans_id, null, null, null, $modified_args ) );
			}
		}
	}
}
