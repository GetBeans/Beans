<?php
/**
 * Tests for beans_replace_action_arguments()
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
 * Class Tests_BeansReplaceActionArguments
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansReplaceActionArguments extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_arguments() should store the "replaced" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$replaced_action = array(
			'args' => 10,
		);

		foreach ( array_keys( static::$test_actions ) as $beans_id ) {
			// Test that the original action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );

			// Now store away the "replace" args.
			$this->assertFalse( beans_replace_action_arguments( $beans_id, $replaced_action['args'] ) );

			// Check that it was stored as "modified".
			$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}

	/**
	 * Test beans_replace_action_arguments() should store the "replaced" args when the original action
	 * has not yet been registered.  Once the original action is registered, then the args should be replaced.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_replace_the_args() {
		$replaced_action = array(
			'args' => 10,
		);

		// Now replace the actions.
		foreach ( static::$test_ids as $beans_id ) {
			beans_replace_action_arguments( $beans_id, $replaced_action['args'] );
		}

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we add the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $replaced_action ) {
					// Check that the number of arguments was replaced in WordPress.
					$this->assertSame( $replaced_action['args'], $args );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['priority'], $priority );
				} );

			// Now add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Check that only the number of arguments was replaced.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $replaced_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
		}
	}

	/**
	 * Test beans_replace_action_arguments() should return false when no args is passed.
	 */
	public function test_should_return_false_when_no_args() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check it returns false.
			$this->assertFalse( beans_replace_action_arguments( $beans_id, '' ) );

			// Verify that it did not get stored in "replaced" or "modified".
			global $_beans_registered_actions;
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['replaced'] );
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action has not been replaced.
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_replace_action_arguments() should replace the registered action's args.
	 */
	public function test_should_replace_the_action_args() {
		$replaced_action = array(
			'args' => 6,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we replace the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $replaced_action ) {
					// Check that the number of arguments was replaced in WordPress.
					$this->assertSame( $replaced_action['args'], $args );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['priority'], $priority );
				} );

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, null, null, $replaced_action['args'] ) );

			// Check that only the number of arguments was replaced.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $original_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $replaced_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
		}
	}
}
