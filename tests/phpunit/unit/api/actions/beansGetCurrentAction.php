<?php
/**
 * Tests for _beans_get_current_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansGetCurrentAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGetCurrentAction extends Actions_Test_Case {

	/**
	 * Test _beans_get_current_action() should return false when the ID is registered as a "removed" action.
	 */
	public function test_should_return_false_when_removed_status() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['removed'][ $beans_id ] = wp_json_encode( $action );

			// Test that it returns false.
			$this->assertFalse( _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return false when the action is invalid.
	 */
	public function test_should_return_false_when_action_is_invalid() {
		global $_beans_registered_actions;

		// Test "added" status.
		$_beans_registered_actions['added']['foo'] = wp_json_encode( array( 'hook' => 'foo' ) );
		$this->assertFalse( _beans_get_current_action( 'foo' ) );

		// Test "modified" status.
		$_beans_registered_actions['modified']['bar'] = wp_json_encode( array(
			'hook'     => 'bar',
			'priority' => 1,
		) );
		$this->assertFalse( _beans_get_current_action( 'bar' ) );

		// Test merging "modified" into "added" status.
		$_beans_registered_actions['modified']['foo'] = wp_json_encode( array( 'callback' => 'foo_cb' ) );
		$this->assertFalse( _beans_get_current_action( 'foo' ) );
	}

	/**
	 * Test _beans_get_current_action() should return the "added" action.
	 */
	public function test_should_return_added_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['added'][ $beans_id ] = wp_json_encode( $action );

			// Test that we get the "added" action.
			$this->assertSame( $action, _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return the "modified" action.
	 */
	public function test_should_return_modified_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['modified'][ $beans_id ] = wp_json_encode( $action );

			// Test that we get the "modified" action.
			$this->assertSame( $action, _beans_get_current_action( $beans_id ) );
		}
	}

	/**
	 * Test _beans_get_current_action() should return the merged "added" and "modified" action.
	 */
	public function test_should_return_merged_added_and_modified() {
		global $_beans_registered_actions;

		$modified_action = array(
			'callback' => 'callback',
			'priority' => 27,
			'args'     => 14,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['added'][ $beans_id ]    = wp_json_encode( $action );
			$_beans_registered_actions['modified'][ $beans_id ] = wp_json_encode( $modified_action );

			// Test that it merges the action.
			$this->assertSame(
				array_merge( $action, $modified_action ),
				_beans_get_current_action( $beans_id )
			);
		}
	}
}
