<?php
/**
 * Tests for beans_replace_action_hook().
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\API\Actions\Includes\Replace_Action_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-replace-action-test-case.php';

/**
 * Class Tests_BeansReplaceActionHook
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansReplaceActionHook extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_hook() should return false when the hook is empty or not a string.
	 */
	public function test_should_return_false_when_hook_is_empty_or_not_string() {

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( 'beans_replace_action' )->never();

			$this->assertFalse( beans_replace_action_hook( $beans_id, '' ) );
			$this->assertFalse( beans_replace_action_hook( $beans_id, [ 'not-a-string' ] ) );
		}
	}

	/**
	 * Test beans_replace_action_hook() should invoke beans_replace_action().
	 */
	public function test_should_invoke_beans_replace_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( 'beans_replace_action' )
				->once()
				->with( $beans_id, $action['hook'] )
				->andReturn( true );

			$this->assertTrue( beans_replace_action_hook( $beans_id, $action['hook'] ) );
		}
	}
}
