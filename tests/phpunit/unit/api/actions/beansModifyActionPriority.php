<?php
/**
 * Tests for beans_modify_action_priority()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionPriority
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyActionPriority extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_priority() should return false when new priority is a non-integer.
	 */
	public function test_should_return_false_when_priority_is_non_integer() {
		$priorities = array(
			null,
			array( 10 ),
			false,
			'',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			foreach ( $priorities as $priority ) {
				$this->assertFalse( beans_modify_action_priority( $beans_id, $priority ) );

				// Check that the priority did not get stored as "modified" in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			}
		}
	}

	/**
	 * Test beans_modify_action_priority() should modify the action's priority when the new one is zero.
	 */
	public function test_should_modify_action_when_priority_is_zero() {
		$priorities = array( 0, 0.0, '0', '0.0' );

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			foreach ( $priorities as $priority ) {
				$this->assertTrue( beans_modify_action_priority( $beans_id, $priority ) );
				$this->assertEquals( array( 'priority' => (int) $priority ), _beans_get_action( $beans_id, 'modified' ) );
			}
		}
	}

	/**
	 * Test beans_modify_action_priority() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

			// Check that it returns false.
			$this->assertFalse( beans_modify_action_priority( $beans_id, $action['priority'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertEquals( array( 'priority' => $action['priority'] ), _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}
}
