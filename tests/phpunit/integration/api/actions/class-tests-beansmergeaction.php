<?php
/**
 * Tests for _beans_merge_action().
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansMergeAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansMergeAction extends Actions_Test_Case {

	/**
	 * The registered actions' status.
	 *
	 * @var array
	 */
	protected $statuses = [ 'added', 'modified', 'removed', 'replaced' ];

	/**
	 * Test _beans_set_action() should merge the new action's configuration with the registered one and then return it.
	 */
	public function test_should_merge_and_return() {
		global $_beans_registered_actions;

		$modified_action = [
			'priority' => 29,
		];

		foreach ( static::$test_actions as $beans_id => $action ) {
			$merged_action = array_merge( $action, $modified_action );

			// Test each status.
			foreach ( $this->statuses as $status ) {
				$_beans_registered_actions[ $status ][ $beans_id ] = $action;
				$this->assertSame( $merged_action, _beans_merge_action( $beans_id, $modified_action, $status ) );
			}
		}
	}

	/**
	 * Test _beans_merge_action() should store an unregistered action.
	 */
	public function test_should_store_new_action() {
		global $_beans_registered_actions;

		foreach ( static::$test_actions as $beans_id => $action ) {

			// Test each status.
			foreach ( $this->statuses as $status ) {
				$this->assertSame( $action, _beans_merge_action( $beans_id, $action, $status ) );
				$this->assertSame( $action, $_beans_registered_actions[ $status ][ $beans_id ] );
			}
		}
	}
}
