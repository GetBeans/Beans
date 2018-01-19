<?php
/**
 * Tests for beans_replace_action_callback()
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
 * Class Tests_BeansReplaceActionCallback
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansReplaceActionCallback extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_callback() should store the "replaced" action when the original action
	 * has not yet been registered.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_when_action_is_not_registered() {
		$replaced_action = array(
			'callback' => 'my_new_callback',
		);

		foreach ( array_keys( static::$test_actions ) as $beans_id ) {
			// Test that the original action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );

			// Now store away the "replace" callback.
			$this->assertFalse( beans_replace_action_callback( $beans_id, $replaced_action['callback'] ) );

			// Check that it was stored as "modified".
			$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}

	/**
	 * Test beans_replace_action_callback() should store the "replaced" callback when the original action
	 * has not yet been registered.  Once the original action is registered, then the callback should be replaced.
	 *
	 * Intent: We are testing to ensure Beans is "load order" agnostic.
	 */
	public function test_should_store_and_then_replace_the_callback() {
		$replaced_action = array(
			'callback' => 'foo',
		);

		// Now replace the actions.
		foreach ( static::$test_ids as $beans_id ) {
			beans_replace_action_callback( $beans_id, $replaced_action['callback'] );
		}

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we add the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $replaced_action ) {
					// Check that the callback was replaced in WordPress.
					$this->assertSame( $replaced_action['callback'], $callback );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['priority'], $priority );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Now add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Check that only the callback was replaced.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $replaced_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans and WordPress.
			$this->check_stored_in_beans( $beans_id, $replaced_action );
		}
	}

	/**
	 * Test beans_replace_action_callback() should return false when no callback was passed.
	 */
	public function test_should_return_false_when_no_callback() {
		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $action_config ) {
			// Check it returns false.
			$this->assertFalse( beans_replace_action_callback( $beans_id, '' ) );

			// Verify that it did not get stored in "replaced" or "modified".
			global $_beans_registered_actions;
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['replaced'] );
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action has not been replaced.
			$this->assertSame( $action_config, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_replace_action_callback() should replace the registered action's callback.
	 */
	public function test_should_replace_the_action_callback() {
		$replaced_action = array(
			'callback' => 'foo',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we replace the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $replaced_action ) {
					// Check that the callback was replaced in WordPress.
					$this->assertSame( $replaced_action['callback'], $callback );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['priority'], $priority );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Run the replace.
			$this->assertTrue( beans_replace_action( $beans_id, null, $replaced_action['callback'] ) );

			// Check that only the callback was replaced.
			$new_action = _beans_get_action( $beans_id, 'added' );
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $replaced_action['callback'], $new_action['callback'] );
			$this->assertEquals( $original_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Check that the "replaced" action has been stored in Beans.
			$this->check_stored_in_beans( $beans_id, $replaced_action );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );

			// Check that the replace action was added in WordPress.
			$this->assertTrue( has_action( $original_action['hook'], $replaced_action['callback'] ) !== false );
		}
	}
}
