<?php
/**
 * Tests for beans_modify_action_priority()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionPriority
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansModifyActionPriority extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_priority() should return false when new priority is a non-integer.
	 */
	public function test_should_return_false_when_priority_is_non_integer() {
		global $wp_filter;

		$priorities = array(
			null,
			array( 10 ),
			false,
			'',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertEquals(
				array(
					'function'      => $original_action['callback'],
					'accepted_args' => $original_action['args'],
				),
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]
			);

			foreach ( $priorities as $priority ) {
				// Check that it returns false.
				$this->assertFalse( beans_modify_action_priority( $beans_id, $priority ) );

				// Check that the priority did not get stored as "modified" in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

				// Check that the priority did not change in WordPress.
				$this->assertEquals(
					array(
						'function'      => $original_action['callback'],
						'accepted_args' => $original_action['args'],
					),
					$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]
				);
			}
		}
	}

	/**
	 * Test beans_modify_action_priority() should modify the action's priority when the new one is zero.
	 */
	public function test_should_modify_action_when_priority_is_zero() {
		global $wp_filter;

		$priorities = array( 0, 0.0, '0', '0.0' );

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertEquals(
				array(
					'function'      => $original_action['callback'],
					'accepted_args' => $original_action['args'],
				),
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]
			);

			foreach ( $priorities as $priority ) {
				// Modify the priority.
				$this->assertTrue( beans_modify_action_priority( $beans_id, $priority ) );

				// Check that the modified action is registered as "modified" in Beans.
				$this->assertEquals( array( 'priority' => (int) $priority ), _beans_get_action( $beans_id, 'modified' ) );

				// Check that the priority did change in WordPress.
				$this->check_modified_in_wp( $original_action, (int) $priority );
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

	/**
	 * Test beans_modify_action_priority() should modify the registered action's priority level.
	 */
	public function test_should_modify_the_action_priority() {
		global $wp_filter;

		$modified_action = array(
			'priority' => 20,
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertEquals(
				array(
					'function'      => $original_action['callback'],
					'accepted_args' => $original_action['args'],
				),
				$wp_filter[ $original_action['hook'] ]->callbacks[ $original_action['priority'] ][ $original_action['callback'] ]
			);

			// Modify the action's priority.
			$this->assertTrue( beans_modify_action_priority( $beans_id, $modified_action['priority'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the priority did change in WordPress.
			$this->check_modified_in_wp( $original_action, $modified_action['priority'] );
		}
	}

	/**
	 * Check that the priority was modified in WordPress.
	 *
	 * @since 1.5.0
	 *
	 * @param array $original_action The original action.
	 * @param int   $new_priority    The new priority.
	 *
	 * @return void
	 */
	protected function check_modified_in_wp( $original_action, $new_priority ) {
		global $wp_filter;

		$callbacks_in_wp = $wp_filter[ $original_action['hook'] ]->callbacks;

		// Check that the original action was removed from WordPress.
		if ( isset( $callbacks_in_wp[ $original_action['priority'] ] ) ) {
			$this->assertArrayNotHasKey( $original_action['callback'], $callbacks_in_wp[ $original_action['priority'] ] );
		} else {
			$this->assertArrayNotHasKey( $original_action['priority'], $callbacks_in_wp );
		}

		// Check that the action's priority was modified in WordPress.
		$this->assertEquals(
			array(
				'function'      => $original_action['callback'],
				'accepted_args' => $original_action['args'],
			),
			$callbacks_in_wp[ $new_priority ][ $original_action['callback'] ]
		);
	}
}
