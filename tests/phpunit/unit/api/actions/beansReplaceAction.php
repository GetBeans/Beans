<?php
/**
 * Tests for beans_replace_action().
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
 * Class Tests_BeansReplaceAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansReplaceAction extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action() should return false when there's nothing to replace,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_modify() {

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check that neither of these functions are called.
			Monkey\Functions\expect( '_beans_merge_action' )->never();
			Monkey\Functions\expect( 'beans_modify_action' )->never();

			$this->assertFalse( beans_replace_action( $beans_id ) );
		}
	}

	/**
	 * Test beans_replace_action() should store the "replaced" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$replaced_callback = 'my_new_callback';
		$replaced_action   = array( 'callback' => $replaced_callback );

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( '_beans_merge_action' )
				// Simulate that the replaced action gets stored in the "replaced" container.
				->once()
				->with( $beans_id, $replaced_action, 'replaced' )
				->ordered()
				->andReturn( $replaced_action )
				->andAlsoExpectIt()
				// Check that it does not call it to merge with "added".
				->with( $beans_id, $replaced_action, 'added' )
				->never();

			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, null, $replaced_callback, null, null )
				->andReturn( false );

			// Run the replace.
			$this->assertFalse( beans_replace_action( $beans_id, null, $replaced_callback ) );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's hook.
	 */
	public function test_should_replace_the_action_hook() {
		$replaced_hook   = 'foo';
		$replaced_action = array( 'hook' => $replaced_hook );

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( '_beans_merge_action' )
				// Simulate that the replaced action gets stored in the "replaced" container.
				->once()
				->with( $beans_id, $replaced_action, 'replaced' )
				->ordered()
				->andReturn( $replaced_action )
				->andAlsoExpectIt()
				// Simulate that the "replaced" hook overwrites the "added" hook.
				->once()
				->with( $beans_id, $replaced_action, 'added' );

			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, $replaced_hook, null, null, null )
				->andReturn( true ); // Simulate that the original action was replaced.

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, $replaced_hook ) );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's callback.
	 */
	public function test_should_replace_the_action_callback() {
		$replaced_callback   = 'my_replaced_callback';
		$replaced_action = array( 'callback' => $replaced_callback );

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( '_beans_merge_action' )
				// Simulate that the replaced action gets stored in the "replaced" container.
				->once()
				->with( $beans_id, $replaced_action, 'replaced' )
				->ordered()
				->andReturn( $replaced_action )
				->andAlsoExpectIt()
				// Simulate that the "replaced" hook overwrites the "added" hook.
				->once()
				->with( $beans_id, $replaced_action, 'added' );

			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, null, $replaced_callback, null, null )
				->andReturn( true ); // Simulate that the original action was replaced.

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, $replaced_callback ) );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's priority level.
	 */
	public function test_should_replace_the_action_priority() {
		$replaced_priority   = -2;
		$replaced_action = array( 'priority' => $replaced_priority );

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( '_beans_merge_action' )
				// Simulate that the replaced action gets stored in the "replaced" container.
				->once()
				->with( $beans_id, $replaced_action, 'replaced' )
				->ordered()
				->andReturn( $replaced_action )
				->andAlsoExpectIt()
				// Simulate that the "replaced" hook overwrites the "added" hook.
				->once()
				->with( $beans_id, $replaced_action, 'added' );

			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, null, null, $replaced_priority, null )
				->andReturn( true ); // Simulate that the original action was replaced.

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, null, $replaced_priority ) );
		}
	}

	/**
	 * Test beans_replace_action() should replace the registered action's number of arguments.
	 */
	public function test_should_replace_the_action_args() {
		$replaced_args   = 6;
		$replaced_action = array( 'args' => $replaced_args );

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( '_beans_merge_action' )
				// Simulate that the replaced action gets stored in the "replaced" container.
				->once()
				->with( $beans_id, $replaced_action, 'replaced' )
				->ordered()
				->andReturn( $replaced_action )
				->andAlsoExpectIt()
				// Simulate that the "replaced" hook overwrites the "added" hook.
				->once()
				->with( $beans_id, $replaced_action, 'added' );

			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, null, null, null, $replaced_args )
				->andReturn( true ); // Simulate that the original action was replaced.

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, null, null, $replaced_args ) );
		}
	}
}
