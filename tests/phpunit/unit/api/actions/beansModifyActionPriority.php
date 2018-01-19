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
use Brain\Monkey;

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

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {

			foreach ( $priorities as $priority ) {
				// Check that it returns false.
				$this->assertFalse( beans_modify_action_priority( $beans_id, $priority ) );

				// Check that the priority did not get stored as "modified" in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			}
		}
	}

	/**
	 * Test beans_modify_action_priority() should modify the action's priority when the new priority level is zero.
	 */
	public function test_should_modify_action_when_priority_is_zero() {
		$priorities = array( 0, 0.0, '0', '0.0' );

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {

			foreach ( $priorities as $modified_priority ) {
				// Set up the WordPress simulator before we modify the action.
				Monkey\Actions\expectAdded( $original_action['hook'] )
					->once()
					->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_priority ) {
						// Check that the priority was modified in WordPress.
						$this->assertSame( (int) $modified_priority, $priority );
						// Check that the other parameters remain unchanged.
						$this->assertSame( $original_action['callback'], $callback );
						$this->assertSame( $original_action['args'], $args );
					} );

				// Modify the priority.
				$this->assertTrue( beans_modify_action_priority( $beans_id, $modified_priority ) );

				// Check that the modified action is registered as "modified" in Beans.
				$this->assertEquals( array( 'priority' => (int) $modified_priority ), _beans_get_action( $beans_id, 'modified' ) );
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
	 * Test beans_modify_action() should modify the registered action's priority level.
	 */
	public function test_should_modify_the_action_priority() {
		$modified_action = array(
			'priority' => 20,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_action ) {
					// Check that the priority was modified in WordPress.
					$this->assertSame( $modified_action['priority'], $priority );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Modify the action's priority.
			$this->assertTrue( beans_modify_action_priority( $beans_id, $modified_action['priority'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}
}
