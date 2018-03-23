<?php
/**
 * Tests for beans_replace_action_arguments().
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
 * Class Tests_BeansReplaceActionArguments
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansReplaceActionArguments extends Replace_Action_Test_Case {

	/**
	 * Test beans_replace_action_arguments() should invoke beans_replace_action().
	 */
	public function test_should_invoke_beans_replace_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			Monkey\Functions\expect( 'beans_replace_action' )
				->once()
				->with( $beans_id, null, null, null, $action['args'] )
				->andReturn( true );

			$this->assertTrue( beans_replace_action_arguments( $beans_id, $action['args'] ) );
		}
	}
}
