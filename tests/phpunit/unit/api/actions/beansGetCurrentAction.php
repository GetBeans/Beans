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
	 * Test _beans_get_current_action() should return false when the ID is registered with the "removed" status.
	 */
	public function test_should_return_false_when_removed_status() {
		global $_beans_registered_actions;
		$_beans_registered_actions['removed']['foo'] = wp_json_encode( array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		) );

		$this->assertFalse( _beans_get_current_action( 'foo' ) );
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
	 * Test _beans_get_current_action() should return the "added" configuration.
	 */
	public function test_should_return_added_action() {
		global $_beans_registered_actions;
		$action                                    = array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		);
		$_beans_registered_actions['added']['foo'] = wp_json_encode( $action );
		$this->assertEquals( $action, _beans_get_current_action( 'foo' ) );
	}

	/**
	 * Test _beans_get_current_action() should return the "modified" configuration.
	 */
	public function test_should_return_modified_action() {
		global $_beans_registered_actions;
		$action                                       = array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		);
		$_beans_registered_actions['modified']['foo'] = wp_json_encode( $action );
		$this->assertEquals( $action, _beans_get_current_action( 'foo' ) );
	}

	/**
	 * Test _beans_get_current_action() should merge "modified" action's configuration with the "added" configuration.
	 */
	public function test_should_merge_modified_with_added() {
		global $_beans_registered_actions;

		$_beans_registered_actions['added']['foo'] = wp_json_encode( array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		) );

		$_beans_registered_actions['modified']['foo'] = wp_json_encode( array(
			'priority' => 1,
		) );

		$expected = array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 1,
			'args'     => 1,
		);
		$this->assertEquals( $expected, _beans_get_current_action( 'foo' ) );
	}
}
