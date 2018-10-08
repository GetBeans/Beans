<?php
/**
 * Tests for beans_reset_action().
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
 * @group   api
 * @group   api-actions
 */
class Tests_BeansResetAction extends Actions_Test_Case {

	/**
	 * Test beans_reset_action() should return false when the action is not registered.
	 */
	public function test_should_return_false_when_no_action_is_registered() {
		Monkey\Functions\when( '_beans_unset_action' );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Simulate that there is no action registered.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'added' )
				->andReturn( false );

			// Check that it does bail out and not call these functions.
			Monkey\Functions\expect( '_beans_get_current_action' )->never();
			Monkey\Functions\expect( 'remove_action' )->never();
			Monkey\Functions\expect( 'add_action' )->never();

			$this->assertFalse( beans_reset_action( $beans_id ) );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action after it was "removed".
	 */
	public function test_should_reset_after_remove() {
		Monkey\Functions\when( '_beans_unset_action' );

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Simulate that the original action is registered.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'added' )
				->andReturn( $action );
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( $action );

			// Expect the add_action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->with( $action['callback'], $action['priority'], $action['args'] );

			// Reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the original action is registered in WordPress.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_reset_action() should reset the original action after modifying it.
	 */
	public function test_should_reset_after_modifying_the_action() {
		Monkey\Functions\when( '_beans_unset_action' );

		$modified_action = [
			'callback' => 'my_new_callback',
			'priority' => 47,
		];

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Simulate modifying the action.
			add_action( $action['hook'], $modified_action['callback'], $modified_action['priority'], $action['args'] );

			// Simulate that the original action is registered.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'added' )
				->andReturn( $action );
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturnUsing(
					function() use ( $action, $modified_action ) {
							return array_merge( $action, $modified_action );
					}
				);

			// Expect the add_action.
			Monkey\Actions\expectAdded( $action['hook'] )
				->once()
				->with( $action['callback'], $action['priority'], $action['args'] );

			// Reset the action.
			$this->assertSame( $action, beans_reset_action( $beans_id ) );

			// Check that the action is restored in WordPress.
			$this->assertTrue( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( has_action( $action['hook'], $modified_action['callback'] ) );
		}
	}

	/**
	 * Test beans_reset_action() should not reset after replacing the action.
	 *
	 * Why? "Replace" overwrites and is not resettable, meaning it stores in the "added" to overwrite it.
	 */
	public function test_should_not_reset_after_replacing_action() {
		Monkey\Functions\when( '_beans_unset_action' );

		foreach ( static::$test_actions as $beans_id => $action ) {
			$replaced_action = array_merge( $action, [ 'hook' => 'replaced_hook' ] );

			// Simulate that the replaced action is registered.
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'added' )
				->andReturn( $replaced_action );
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( $replaced_action );

			// Expect the add_action.
			Monkey\Actions\expectAdded( $replaced_action['hook'] )
				->once()
				->with( $action['callback'], $action['priority'], $action['args'] );

			// Reset the action.
			$this->assertSame( $replaced_action, beans_reset_action( $beans_id ) );

			// Check in WordPress.
			$this->assertTrue( has_action( $replaced_action['hook'], $replaced_action['callback'] ) );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}
}
