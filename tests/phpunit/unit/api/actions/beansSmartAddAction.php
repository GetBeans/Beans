<?php
/**
 * Tests for beans_add_smart_action().
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
 * Class Tests_BeansSmartAddAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansSmartAddAction extends Actions_Test_Case {

	/**
	 * Test beans_add_smart_action() should invoke beans_add_action().
	 */
	public function test_should_invoke_beans_add_action() {

		foreach ( static::$test_actions as $action ) {
			Monkey\Functions\expect( 'beans_add_action' )
				->once()
				->with( $action['callback'], $action['hook'], $action['callback'], 10, 1 )
				->andReturn( true );

			$this->assertTrue( beans_add_smart_action( $action['hook'], $action['callback'] ) );
		}
	}
}
