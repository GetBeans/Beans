<?php
/**
 * Tests for beans_modify_action_callback()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionCallback
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   integration-tests
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

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check the starting state.
			$this->assertTrue( has_action( $original_action['hook'], $original_action['callback'] ) !== false );

			foreach ( $callbacks as $callback ) {
				// Check that it returns false.
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

		$this->go_to_post();

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			// Check that the original action is registered in WordPress and in Beans as "added".
			$this->check_registered_in_wp( $original_action['hook'], $original_action );
			$this->assertSame( $original_action, _beans_get_action( $beans_id, 'added' ) );

			// Modify the callback.
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
