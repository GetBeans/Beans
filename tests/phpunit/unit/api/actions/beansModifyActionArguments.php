<?php
/**
 * Tests for beans_modify_action_arguments()
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
 * Class Tests_BeansModifyActionArguments
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyActionArguments extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_arguments() should return false when new args is a non-integer value.
	 */
	public function test_should_return_false_when_args_is_non_integer() {
		$arguments = array(
			null,
			array( 10 ),
			false,
			'',
		);

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {

			foreach ( $arguments as $number_of_args ) {
				$this->assertFalse( beans_modify_action_arguments( $beans_id, $number_of_args ) );

				// Check that the modification was not stored in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );
			}
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the action's "args" when the new one is zero.
	 */
	public function test_should_modify_action_when_args_is_zero() {
		$arguments = array( 0, 0.0, '0', '0.0' );

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {

			foreach ( $arguments as $number_of_args ) {
				// Set up the WordPress simulator before we modify the action.
				Monkey\Actions\expectAdded( $original_action['hook'] )
					->once()
					->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $number_of_args ) {
						// Check that the number of arguments was modified in WordPress.
						$this->assertSame( (int) $number_of_args, $args );
						// Check that the other parameters remain unchanged.
						$this->assertSame( $original_action['callback'], $callback );
						$this->assertSame( $original_action['priority'], $priority );
					} );

				// Modify the action's number of arguments.
				$this->assertTrue( beans_modify_action_arguments( $beans_id, $number_of_args ) );

				// Check that the modified action is registered as "modified" in Beans.
				$this->assertEquals( array( 'args' => (int) $number_of_args ), _beans_get_action( $beans_id, 'modified' ) );
			}
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
			$this->assertFalse( beans_modify_action_arguments( $beans_id, $action['args'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertEquals( array( 'args' => $action['args'] ), _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action() should modify the registered action's number of arguments.
	 */
	public function test_should_modify_the_action_args() {
		$modified_action = array(
			'args' => 7,
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_action ) {
					// Check that the number of arguments was modified in WordPress.
					$this->assertSame( $modified_action['args'], $args );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['callback'], $callback );
					$this->assertSame( $original_action['priority'], $priority );
				} );

			// Modify the action's number of arguments.
			$this->assertTrue( beans_modify_action_arguments( $beans_id, $modified_action['args'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );
		}
	}
}
