<?php
/**
 * Tests for _beans_merge_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansMergeAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansMergeAction extends Actions_Test_Case {

	/**
	 * The registered actions' status.
	 *
	 * @var array
	 */
	protected $statuses = array( 'added', 'modified', 'removed', 'replaced' );

	/**
	 * Test _beans_set_action() should merge the new action's configuration with the registered one and then return it.
	 */
	public function test_should_merge_and_return() {
		global $_beans_registered_actions;

		$modified_action = array(
			'priority' => 29,
		);

		foreach ( static::$test_actions as $beans_id => $action ) {
			$encoded_action = wp_json_encode( $action );
			$merged_action  = array_merge( $action, $modified_action );

			// Test each status.
			foreach ( $this->statuses as $status ) {
				$_beans_registered_actions[ $status ][ $beans_id ] = $encoded_action;
				$this->assertSame( $merged_action, _beans_merge_action( $beans_id, $modified_action, $status ) );
			}
		}
	}

	/**
	 * Test _beans_merge_action() should store a unregistered action.
	 */
	public function test_should_store_new_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {
			$encoded_action = wp_json_encode( $action );

			// Test each status.
			foreach ( $this->statuses as $status ) {
				$this->assertSame( $action, _beans_merge_action( $beans_id, $action, $status ) );
				$this->assertSame( $encoded_action, $_beans_registered_actions[ $status ][ $beans_id ] );
			}
		}
	}
}
