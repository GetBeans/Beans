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
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyActionCallback extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_callback() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$this->assertFalse( beans_modify_action_callback( 'foo', null ) );
		$this->assertFalse( beans_modify_action_callback( 'foo', 'my_new_callback' ) );
	}

	/**
	 * Test beans_modify_action_callback() should return false when null is the new callback.
	 */
	public function test_should_return_false_when_null_is_new_callback() {
		$this->setup_original_action();
		$this->assertFalse( beans_modify_action_callback( 'foo', null ) );

		$this->setup_original_action( 'beans' );
		$this->assertFalse( beans_modify_action_callback( 'beans', null ) );
	}

	/**
	 * Test beans_modify_action_callback() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'callback' => 'my_new_callback',
		);
		$this->assertTrue( beans_modify_action_callback( 'beans', $modified_action['callback'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( array_merge( $action, $modified_action ) );
	}
}
