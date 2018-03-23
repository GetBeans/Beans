<?php
/**
 * Tests for beans_modify_action_callback().
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
 * @group   api
 * @group   api-actions
 */
class Tests_BeansModifyActionCallback extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_callback() should return false when the callable is empty.
	 */
	public function test_should_return_false_when_not_callable() {

		foreach ( static::$test_ids as $beans_id ) {
			Monkey\Functions\expect( 'beans_modify_action' )->never();

			$this->assertFalse( beans_modify_action_callback( $beans_id, null ) );
		}
	}

	/**
	 * Test beans_modify_action_callback() should invoke beans_modify_action().
	 */
	public function test_should_invoke_beans_modify_action() {

		foreach ( static::$test_actions as $beans_id => $original_action ) {
			Monkey\Functions\expect( 'beans_modify_action' )
				->once()
				->with( $beans_id, null, $original_action['callback'] )
				->andReturn( true );

			$this->assertTrue( beans_modify_action_callback( $beans_id, $original_action['callback'] ) );
		}
	}
}
