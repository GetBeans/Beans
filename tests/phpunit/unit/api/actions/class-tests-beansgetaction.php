<?php
/**
 * Tests for _beans_get_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansGetAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansGetAction extends Actions_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		Monkey\Functions\when( '_beans_unique_action_id' )->returnArg();
	}

	/**
	 * Test _beans_get_action() should return false when registry is empty.
	 */
	public function test_should_return_false_when_registry_is_empty() {
		global $_beans_registered_actions;

		foreach ( array_keys( $_beans_registered_actions ) as $status ) {
			$this->assertEmpty( $_beans_registered_actions[ $status ] );
			$this->assertFalse( _beans_get_action( 'foo', $status ) );
		}
	}

	/**
	 * Test _beans_get_action() should return false when the action is not registered.
	 */
	public function test_should_return_false_when_action_is_not_registered() {

		foreach ( static::$test_ids as $beans_id ) {
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'removed' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'replaced' ) );
		}
	}

	/**
	 * Test _beans_get_action() should return the "added" action.
	 */
	public function test_should_return_added_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['added'][ $beans_id ] = $action;

			// Test that we get the "added" action.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'added' ) );

			// Make sure that it is not stored in the other registries.
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'removed' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'replaced' ) );
		}
	}

	/**
	 * Test _beans_get_action() should return the "modified" action.
	 */
	public function test_should_return_modified_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['modified'][ $beans_id ] = $action;

			// Test that we get the "modified" action.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'modified' ) );

			// Make sure that it is not stored in the other registries.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'removed' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'replaced' ) );
		}
	}

	/**
	 * Test _beans_get_action() should return the "removed" action.
	 */
	public function test_should_return_removed_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['removed'][ $beans_id ] = $action;

			// Test that we get the "removed" action.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'removed' ) );

			// Make sure that it is not stored in the other registries.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'replaced' ) );
		}
	}

	/**
	 * Test _beans_get_action() should return the "replaced" action.
	 */
	public function test_should_return_replaced_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Store the action in the registry.
			$_beans_registered_actions['replaced'][ $beans_id ] = $action;

			// Test that we get the "replaced" action.
			$this->assertSame( $action, _beans_get_action( $beans_id, 'replaced' ) );

			// Make sure that it is not stored in the other registries.
			$this->assertFalse( _beans_get_action( $beans_id, 'added' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'removed' ) );
		}
	}
}
