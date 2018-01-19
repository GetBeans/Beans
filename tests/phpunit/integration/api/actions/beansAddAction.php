<?php
/**
 * Tests for beans_add_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansAddAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansAddAction extends Actions_Test_Case {

	/**
	 * Test beans_add_action() should add the action in both Beans and WordPress.
	 */
	public function test_should_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Test that the action has not yet been added.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );

			// Let's add it.
			$this->assertTrue( beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Now check that it was registered in Beans and WordPress.
			$this->assertEquals( $action, _beans_get_action( $beans_id, 'added' ) );
			$this->check_registered_in_wp( $action['hook'], $action );
		}
	}

	/**
	 * Test beans_add_action() should overwrite the action in both Beans and WordPress.
	 *
	 * This test makes sure nothing breaks if beans_add_action() is called more than once
	 * with the exact same set of conditions.
	 */
	public function test_should_overwrite_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Let's add it the first time.
			$this->assertTrue( beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Add it again.
			$this->assertTrue( beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Now check that it's still registered in both Beans and WordPress.
			$this->assertEquals( $action, _beans_get_action( $beans_id, 'added' ) );
			$this->check_registered_in_wp( $action['hook'], $action );
		}
	}

	/**
	 * Test beans_add_action() should use the action configuration in "replaced" status, when it's available.
	 */
	public function test_should_use_replaced_action_when_available() {
		$replaced_action = array(
			'callback' => 'my_new_callback',
			'priority' => 47,
		);

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// We want to store the "replaced" action first, before we add the original action.
			_beans_set_action( $beans_id, $replaced_action, 'replaced', true );

			// Next, add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Get the newly created action.
			$new_action = _beans_get_action( $beans_id, 'added' );

			// Check if the callback and priority were replaced.
			$this->assertEquals( $original_action['hook'], $new_action['hook'] );
			$this->assertEquals( $replaced_action['callback'], $new_action['callback'] );
			$this->assertEquals( $replaced_action['priority'], $new_action['priority'] );
			$this->assertEquals( $original_action['args'], $new_action['args'] );

			// Now check that the action was replaced in WordPress.
			$this->check_registered_in_wp( $new_action['hook'], $new_action );
		}
	}

	/**
	 * Test beans_add_action() should return false when the ID is registered to the "removed" status.
	 */
	public function test_should_return_false_when_removed() {
		$empty_action = array(
			'hook'     => null,
			'callback' => null,
			'priority' => null,
			'args'     => null,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the "removed" action before we call beans_add_action().
			_beans_set_action( $beans_id, $empty_action, 'removed', true );

			// Next, add the action.
			beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] );

			// Check if the action is stored as "added".
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );

			// Check that the action is not registered in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_add_action() should merge the "modified" action configuration parameters.
	 */
	public function test_should_merge_modified_action_parameters() {
		$modified_action = array(
			'callback' => 'foo',
			'priority' => 17,
		);

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// We want to store the "modified" action first, before we add the original action.
			_beans_set_action( $beans_id, $modified_action, 'modified', true );

			// Next, add the original action.
			beans_add_action( $beans_id, $original_action['hook'], $original_action['callback'], $original_action['priority'], $original_action['args'] );

			// Test that the original action is stored away, which allows us to reset it (if we want).
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );

			// Now check that the action was modified in WordPress.
			$new_action = array_merge( $original_action, $modified_action );
			$this->check_registered_in_wp( $original_action['hook'], $new_action );
		}
	}
}
