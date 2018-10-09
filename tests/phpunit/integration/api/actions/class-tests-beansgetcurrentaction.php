<?php
/**
 * Tests for _beans_get_current_action().
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansGetCurrentAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansGetCurrentAction extends Actions_Test_Case {

	/**
	 * Test _beans_get_current_action() should return false when the ID is registered as a "removed" action.
	 */
	public function test_should_return_false_when_removed_status() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['removed'][ $beans_id ] = $action;

			// Test that it returns false.
			$this->assertFalse( _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return the "added" action.
	 */
	public function test_should_return_added_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['added'][ $beans_id ] = $action;

			// Test that we get the "added" action.
			$this->assertSame( $action, _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return false when there's a "modified" action but no "added" action.
	 */
	public function test_should_return_false_when_modified_but_no_added_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['modified'][ $beans_id ] = $action;

			// Run the tests.
			$this->assertFalse( _beans_get_current_action( $beans_id ) );
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['added'] );
			$this->assertSame( $action, $_beans_registered_actions['modified'][ $beans_id ] );
		}
	}

	/**
	 * Test _beans_get_current_action() should return the merged "added" and "modified" action.
	 */
	public function test_should_return_merged_added_and_modified_action() {
		global $_beans_registered_actions;

		$modified_action = [
			'callback' => 'callback',
			'priority' => 27,
			'args'     => 14,
		];

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['added'][ $beans_id ]    = $action;
			$_beans_registered_actions['modified'][ $beans_id ] = $modified_action;

			// Test that it merges the action.
			$this->assertSame(
				array_merge( $action, $modified_action ),
				_beans_get_current_action( $beans_id )
			);
		}
	}
}
