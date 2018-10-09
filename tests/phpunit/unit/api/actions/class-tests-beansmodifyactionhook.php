<?php
/**
 * Tests for beans_modify_action_hook().
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
 * Class Tests_BeansModifyActionHook
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansModifyActionHook extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_hook() should return false when the hook is empty or not a string.
	 */
	public function test_should_return_false_when_hook_is_empty_or_not_string() {

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( 'beans_modify_action' )->never();

			$this->assertFalse( beans_modify_action_hook( $beans_id, '' ) );
			$this->assertFalse( beans_modify_action_hook( $beans_id, [ 'not-a-string' ] ) );
		}
	}

	/**
	 * Test beans_modify_action_hook() should invoke beans_modify_action().
	 */
	public function test_should_invoke_beans_modify_action() {

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, $original_action['hook'] )
				->andReturn( true );

			$this->assertTrue( beans_modify_action_hook( $beans_id, $original_action['hook'] ) );
		}
	}
}
