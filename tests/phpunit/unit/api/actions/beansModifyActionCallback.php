<?php
/**
 * Tests for beans_modify_action_callback()
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
 * Class Tests_BeansModifyActionCallback
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyActionCallback extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_callback() should not modify when the callback is invalid.
	 */
	public function test_should_not_modify_when_invalid_callback() {
		$callbacks = array(
			null,
			false,
			'',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {

			foreach ( $callbacks as $callback ) {
				$this->assertFalse( beans_modify_action_callback( $beans_id, $callback ) );

				// Check that the callback did not get stored as "modified" in Beans.
				$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

				// Check that the callback did not change in WordPress.
				$this->assertTrue( has_action( $original_action['hook'], $original_action['callback'] ) !== false );
			}
		}
	}

	/**
	 * Test beans_modify_action_callback() should register with Beans as "modified", but not add the action.
	 */
	public function test_should_register_as_modified_but_not_add_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			// Check the starting state.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
			$this->assertFalse( _beans_get_action( $beans_id, 'modified' ) );

			// Check that it returns false.
			$this->assertFalse( beans_modify_action_callback( $beans_id, $action['callback'] ) );

			// Check that it did register as "modified" in Beans.
			$this->assertEquals( array( 'callback' => $action['callback'] ), _beans_get_action( $beans_id, 'modified' ) );

			// Check that the action was not added in WordPress.
			$this->assertFalse( has_action( $action['hook'], $action['callback'] ) );
		}
	}

	/**
	 * Test beans_modify_action_callback() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$modified_action = array(
			'callback' => 'my_callback',
		);

		$this->go_to_post( true );

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Set up the WordPress simulator before we modify the action.
			Monkey\Actions\expectAdded( $original_action['hook'] )
				->once()
				->whenHappen( function( $callback, $priority, $args ) use ( $original_action, $modified_action ) {
					// Check that the callback was modified in WordPress.
					$this->assertSame( $modified_action['callback'], $callback );
					// Check that the other parameters remain unchanged.
					$this->assertSame( $original_action['priority'], $priority );
					$this->assertSame( $original_action['args'], $args );
				} );

			// Modify the action's callback.
			$this->assertTrue( beans_modify_action_callback( $beans_id, $modified_action['callback'] ) );

			// Check that the modified action is registered as "modified" in Beans.
			$this->assertEquals( $modified_action, _beans_get_action( $beans_id, 'modified' ) );

			// Check that the original action was removed from WordPress.
			$this->assertFalse( has_action( $original_action['hook'], $original_action['callback'] ) );

			// Check that the modified action was added in WordPress.
			$this->assertTrue( has_action( $original_action['hook'], $modified_action['callback'] ) !== false );
		}
	}
}
