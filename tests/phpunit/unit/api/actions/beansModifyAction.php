<?php
/**
 * Tests for beans_modify_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyAction extends Actions_Test_Case {

	/**
	 * Test beans_modify_action() should return false when there's nothing to modify,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_modify() {
		global $_beans_registered_actions;

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			$this->assertFalse( beans_modify_action( $beans_id ) );

			// Verify that the action was not stored as "modified".
			$this->assertArrayNotHasKey( $beans_id, $_beans_registered_actions['modified'] );

			// Check that the original action was not replaced.
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );
		}
	}

	/**
	 * Test beans_modify_action() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

			// Check that it returns false.
			$this->assertFalse( beans_modify_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertEquals( $action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's hook.
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

			// Modify the callback.
			$this->assertTrue( beans_modify_action( $beans_id, $modified_action['hook'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );
			// Check that the modified action was added in WordPress.
			$this->assertTrue( has_action( $modified_action['hook'], $original_action['callback'] ) !== false );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$modified_action = array(
			'callback' => 'my_callback',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {

			// Check that the original action is registered in WordPress and in Beans as "added".
			$this->check_registered_in_wp( $original_action['hook'], $original_action );
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );

			// Modify the callback.
			$this->assertTrue( beans_modify_action( $beans_id, null, $modified_action['callback'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );
			// Check that the modified action was added in WordPress.
			$this->assertTrue( has_action( $original_action['hook'], $modified_action['callback'] ) !== false );
		}
	}
}
