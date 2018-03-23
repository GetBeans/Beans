<?php
/**
 * Tests for beans_replace_action_callback().
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
 * Class Tests_BeansReplaceActionCallback
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansReplaceActionCallback extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_callback() should return false when the callable is empty.
	 */
	public function test_should_return_false_when_not_callable() {

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( 'beans_replace_action' )->never();

			$this->assertFalse( beans_replace_action_callback( $beans_id, null ) );
		}
	}

	/**
	 * Test beans_replace_action_callback() should invoke beans_replace_action().
	 */
	public function test_should_invoke_beans_replace_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( 'beans_replace_action' )
				->once()
				->with( $beans_id, null, $action['callback'] )
				->andReturn( true );

			$this->assertTrue( beans_replace_action_callback( $beans_id, $action['callback'] ) );
		}
	}
}
