<?php
/**
 * Tests for _beans_get_current_action().
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
 * Class Tests_BeansGetCurrentAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansGetCurrentAction extends Actions_Test_Case {

	/**
	 * Test _beans_get_current_action() should return false when the ID is registered as a "removed" action.
	 */
	public function test_should_return_false_when_removed_status() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'removed' )
				->andReturn( true )
				->andAlsoExpectIt()
				->with( $beans_id, 'added' )->never()
				->andAlsoExpectIt()
				->with( $beans_id, 'modified' )->never();

			// Test that it returns false.
			$this->assertFalse( _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return the "added" action.
	 */
	public function test_should_return_added_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'removed' )
				->andReturn( false )
				->andAlsoExpectIt()
				// Simulate getting the "added" action from the container.
				->once()
				->with( $beans_id, 'added' )
				->andReturn( $action )
				->andAlsoExpectIt()
				->once()
				->with( $beans_id, 'modified' )
				->andReturn( false );

			// Test that we get the "added" action.
			$this->assertSame( $action, _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return false when there's a "modified" action but no "added" action.
	 */
	public function test_should_return_false_when_modified_but_no_added() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'removed' )
				->andReturn( false )
				->andAlsoExpectIt()
				// Simulate that an action has not been stored in the "added" container.
				->once()
				->with( $beans_id, 'added' )
				->andReturn( false )
				->andAlsoExpectIt()
				// This one should not get called.
				->with( $beans_id, 'modified' )->never();

			// Run the tests.
			$this->assertFalse( _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return the merged "added" and "modified" action.
	 */
	public function test_should_return_merged_added_and_modified() {
		$modified_action = array(
			'callback' => 'callback',
			'priority' => 27,
			'args'     => 14,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( '_beans_get_action' )
				->once()
				->with( $beans_id, 'removed' )
				->andReturn( false )
				->andAlsoExpectIt()
				// Simulate getting the "added" action from the container.
				->once()
				->with( $beans_id, 'added' )
				->andReturn( $action )
				->andAlsoExpectIt()
				// Simulate getting the "modified" action from the container.
				->once()
				->with( $beans_id, 'modified' )
				->andReturn( $modified_action );

			// Test that it merges the action.
			$this->assertSame(
				array_merge( $action, $modified_action ),
				_beans_get_current_action( $beans_id )
			);
		}
	}
}
