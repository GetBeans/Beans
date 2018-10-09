<?php
/**
 * Tests for beans_remove_action().
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
 * Class Tests_BeansRemoveAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansRemoveAction extends Replace_Action_Test_Case {

	/**
	 * Test beans_remove_action() should store the "removed" action before the original action is "added".
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$expected = [
			'hook'     => null,
			'callback' => null,
			'priority' => null,
			'args'     => null,
		];

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Simulate that there is no "added" action.
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( false );

			// Check that remove_action does not get called.
			Monkey\Functions\expect( 'remove_action' )->never();

			// Simulate storing the action as "removed".
			Monkey\Functions\expect( '_beans_set_action' )
				->once()
				->with( $beans_id, $expected, 'removed' )
				->andReturn( $expected );

			// Run the remove.
			$this->assertSame( $expected, beans_remove_action( $beans_id ) );
		}
	}

	/**
	 * Test beans_remove_action() should remove the registered action.
	 */
	public function test_should_remove_registered_action() {

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Simulate that the original action is "added".
			Monkey\Functions\expect( '_beans_get_current_action' )
				->once()
				->with( $beans_id )
				->andReturn( $original_action );

			// Expect that the original action gets removed.
			Monkey\Functions\expect( 'remove_action' )
				->once()
				->with( $original_action['hook'], $original_action['callback'], $original_action['priority'] );

			// Simulate storing the action as "removed".
			Monkey\Functions\expect( '_beans_set_action' )
				->once()
				->with( $beans_id, $original_action, 'removed' )
				->andReturn( $original_action );

			// Run the remove.
			$this->assertSame( $original_action, beans_remove_action( $beans_id ) );
		}
	}
}
