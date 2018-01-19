<?php
/**
 * Tests for beans_modify_action_arguments()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionArguments
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansModifyActionArguments extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_arguments() should return false when new args is a non-integer value.
	 */
	public function test_should_return_false_when_args_is_non_integer() {
		global $wp_filter;

		$arguments = array(
			null,
			array( 10 ),
			false,
			'',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertEquals(
				$original_action['args'],
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]['accepted_args']
			);

			foreach ( $arguments as $number_of_args ) {
				// Check that it returns false.
				$this->assertFalse( beans_modify_action_arguments( $beans_id, $number_of_args ) );

				// Check that the modification was not stored in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

				// Check that the number of arguments did not change in WordPress.
				$this->assertEquals(
					$original_action['args'],
					$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]['accepted_args']
				);
			}
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the action's "args" when the new one is zero.
	 */
	public function test_should_modify_action_when_args_is_zero() {
		global $wp_filter;

		$arguments = array( 0, 0.0, '0', '0.0' );

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertEquals(
				$original_action['args'],
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]['accepted_args']
			);

			foreach ( $arguments as $number_of_args ) {
				// Modify the action's callback.
				$this->assertTrue( beans_modify_action_arguments( $beans_id, $number_of_args ) );

				// Check that the modified action is registered as "modified" in Beans.
				$this->assertEquals( array( 'args' => (int) $number_of_args ), _beans_get_action( $beans_id, 'modified' ) );

				// Check that the action's number of arguments was modified in WordPress.
				$this->assertEquals(
					$number_of_args,
					$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]['accepted_args']
				);
			}
		}
	}

	/**
	 * Test beans_modify_action_arguments() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

			// Check that it returns false.
			$this->assertFalse( beans_modify_action_arguments( $beans_id, $action['args'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( array( 'args' => $action['args'] ), _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the registered action's number of arguments.
	 */
	public function test_should_modify_the_action_args() {
		global $wp_filter;

		$modified_action = array(
			'args' => 7,
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Test the action before we start.
			$this->assertEquals(
				$original_action['args'],
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]['accepted_args']
			);

			// Modify the action's number of arguments.
			$this->assertTrue( beans_modify_action_arguments( $beans_id, $modified_action['args'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action's number of arguments was modified in WordPress.
			$this->assertEquals(
				$modified_action['args'],
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]['accepted_args']
			);
		}
	}
}
