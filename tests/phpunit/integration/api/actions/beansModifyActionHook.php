<?php
/**
 * Tests for beans_modify_action_hook()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionHook
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansModifyActionHook extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_hook() should not modify the action when the hook is invalid.
	 */
	public function test_should_not_modify_when_invalid_hook() {
		$hooks = array(
			null,
			false,
			array( 'foo' ),
			'',
			0,
			0.0,
			'0',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertTrue( has_action( $original_action['hook'], $original_action['callback'] ) !== false );

			foreach ( $hooks as $hook ) {
				// Check that it returns false.
				$this->assertFalse( beans_modify_action_hook( $beans_id, $hook ) );

				// Check that the hook did not get stored as "modified" in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

				// Check that the hook did not change in WordPress.
				$this->assertTrue( has_action( $original_action['hook'], $original_action['callback'] ) !== false );
			}
		}
	}

	/**
	 * Test beans_modify_action_hook() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

			// Check that it returns false.
			$this->assertFalse( beans_modify_action_hook( $beans_id, $action['hook'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertEquals( array( 'hook' => $action['hook'] ), _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action_hook() should modify the registered action's hook.
	 */
	public function test_should_modify_the_action_hook() {
		$modified_action = array(
			'hook' => 'foo',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check that the original action is registered in WordPress and in Beans as "added".
			$this->check_registered_in_wp( $original_action['hook'], $original_action );
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );

			// Modify the action's hook.
			$this->assertTrue( beans_modify_action_hook( $beans_id, $modified_action['hook'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );

			// Check that the modified action was added in WordPress.
			$this->assertTrue( has_action( $modified_action['hook'], $original_action['callback'] ) !== false );
		}
	}
}
