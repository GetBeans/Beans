<?php
/**
 * Tests for beans_modify_action_priority().
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
 * @group   api
 * @group   api-actions
 */
class Tests_BeansModifyActionPriority extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_priority() should invoke beans_modify_action().
	 */
	public function test_should_invoke_beans_modify_action() {

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, null, null, $original_action['priority'] )
				->andReturn( true );

			$this->assertTrue( beans_modify_action_priority( $beans_id, $original_action['priority'] ) );
		}
	}
}
