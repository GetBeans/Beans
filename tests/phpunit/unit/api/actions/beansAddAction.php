<?php
/**
 * Tests for beans_add_action().
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
 * Class Tests_BeansAddAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansAddAction extends Actions_Test_Case {

	/**
	 * Test beans_add_action() should add the action in both Beans and WordPress.
	 */
	public function test_should_add_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Test that the action has not yet been added.
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['added'] );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );

			// Set up the mocks.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'replaced' )
				->ordered()
				->andReturn( false )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'removed' )
				->ordered()
				->andReturn( false )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'modified' )
				->ordered()
				->andReturn( false );
			Monkey\Functions\expect( '_beans_set_action' )
				->once()
				->with( $beans_id, $action, 'added', true )
				->andReturnUsing( function( $id, $action ) {
					global $_beans_registered_actions;
					$_beans_registered_actions['added'][ $id ] = $action;

					return $action;
				} );
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->with( $action['callback'], $action['priority'], $action['args'] );

			// Let's add the action.
			$this->assertTrue( beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Check that it was registered in Beans and WordPress.
			$this->assertArrayHasKey( $beans_id, $_beans_registered_actions['added'] );
			$this->assertSame( $action, $_beans_registered_actions['added'][ $beans_id ] );
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_add_action() should overwrite the action in both Beans and WordPress.
	 *
	 * This test makes sure nothing breaks if beans_add_action() is called more than once
	 * with the exact same set of conditions.
	 */
	public function test_should_overwrite_add_action() {
		global $_beans_registered_actions;

		Monkey\Functions\when( '_beans_get_action' )->justReturn( false );
		Monkey\Functions\when( '_beans_set_action' )->alias( function( $id, $action ) {
			global $_beans_registered_actions;
			$_beans_registered_actions['added'][ $id ] = $action;

			return $action;
		} );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Let's add it the first time.
			$this->assertTrue( beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Add it again.
			$this->assertTrue( beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Now check that it's still registered in both Beans and WordPress.
			$this->assertArrayHasKey( $beans_id, $_beans_registered_actions['added'] );
			$this->assertSame( $action, $_beans_registered_actions['added'][ $beans_id ] );
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );
		}
	}

	/**
	 * Test beans_add_action() should use the action configuration in "replaced" status, when it's available.
	 */
	public function test_should_use_replaced_action_when_available() {
		global $_beans_registered_actions;
		$replaced_action = array(
			'callback' => 'my_new_callback',
			'priority' => 47,
		);

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the mocks.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'replaced' )
				->ordered()
				->andReturn( $replaced_action ) // Simulates that the replaced action is registered in Beans.
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'removed' )
				->ordered()
				->andReturn( false )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'modified' )
				->ordered()
				->andReturn( false );
			Monkey\Functions\expect( '_beans_set_action' )
				->once()
				->with( $beans_id, array_merge( $original_action, $replaced_action ), 'added', true )
				->andReturnUsing( function( $id, $action ) {
					global $_beans_registered_actions;
					$_beans_registered_actions['added'][ $id ] = $action;

					return $action;
				} );
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->with( $replaced_action['callback'], $replaced_action['priority'], $original_action['args'] );

			// Next, add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Check that the stored action has replaced the callback and priority arguments.
			$new_action = $_beans_registered_actions['added'][ $beans_id ];
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $replaced_action['callback'], $new_action['callback'] );
			$this->assertEquals( $replaced_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Now check that the action was replaced in WordPress.
			$this->assertTrue( has_action( $original_action['hook'], $replaced_action['callback'] ) );
		}
	}

	/**
	 * Test beans_add_action() should return false when the ID is registered to the "removed" status.
	 */
	public function test_should_return_false_when_removed() {
		global $_beans_registered_actions;
		$empty_action = array(
			'hook'     => null,
			'callback' => null,
			'priority' => null,
			'args'     => null,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Set up the mocks.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'replaced' )
				->ordered()
				->andReturn( false )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'removed' )
				->ordered()
				->andReturn( $empty_action )  // Simulates that the "removed" action is registered in Beans.
				->andAlsoExpectIt()
				->with( $beans_id, 'modified' )
				->never();
			Monkey\Functions\expect( '_beans_set_action' )
				->once()
				->with( $beans_id, $action, 'added', true )
				->andReturnUsing( function( $id, $action ) {
					global $_beans_registered_actions;
					$_beans_registered_actions['added'][ $id ] = $action;

					return $action;
				} );
			Monkey\Actions\expectAdded( $action['hook'] )->never();

			// Next, add the action.
			beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] );

			// Check if the action is stored as "added" in Beans.
			$this->assertArrayHasKey( $beans_id, $_beans_registered_actions['added'] );
			$this->assertSame( $action, $_beans_registered_actions['added'][ $beans_id ] );

			// Check that the action is not registered in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_add_action() should merge the "modified" action configuration parameters.
	 */
	public function test_should_merge_modified_action_parameters() {
		global $_beans_registered_actions;
		$modified_action = array(
			'callback' => 'foo',
			'priority' => 17,
		);

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the mocks.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'replaced' )
				->ordered()
				->andReturn( false )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'removed' )
				->ordered()
				->andReturn( false )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'modified' )
				->ordered()
				->andReturn( $modified_action ); // Simulates that the "modified" action is registered in Beans.
			Monkey\Functions\expect( '_beans_set_action' )
				->once()
				->with( $beans_id, $original_action, 'added', true )
				->andReturnUsing( function( $id, $action ) {
					global $_beans_registered_actions;
					$_beans_registered_actions['added'][ $id ] = $action;

					return $action;
				} );
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->with( $modified_action['callback'], $modified_action['priority'], $original_action['args'] );

			// Next, add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Test that the original action is stored away, which allows us to reset it (if we want).
			$this->assertArrayHasKey( $beans_id, $_beans_registered_actions['added'] );
			$this->assertSame( $original_action, $_beans_registered_actions['added'][ $beans_id ] );

			// Now check that the action was modified in WordPress.
			$this->assertTrue( has_action( $original_action['hook'], $modified_action['callback'] ) );
		}
	}
}
