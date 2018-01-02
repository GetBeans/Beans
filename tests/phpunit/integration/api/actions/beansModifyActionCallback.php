<?php
/**
 * Tests for beans_modify_action_callback()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use WP_UnitTestCase;

/**
 * Class Tests_BeansModifyActionCallback
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyActionCallback extends WP_UnitTestCase {

	/**
	 * Reset the test fixture.
	 */
	public function tearDown() {
		parent::tearDown();

		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);
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

	/**
	 * Check that it is not registered first.
	 *
	 * @since 1.5.0
	 *
	 * @param string $id   The ID to check.
	 * @param string $hook The hook (event name) to check.
	 *
	 * @return void
	 */
	protected function check_not_added( $id, $hook ) {
		$this->assertFalse( _beans_get_action( $id, 'added' ) );
		$this->assertFalse( has_action( $hook ) );
	}

	/**
	 * Check that the right parameters are registered in WordPress.
	 *
	 * @since 1.5.0
	 *
	 * @param array $action The action that should be registered.
	 *
	 * @return void
	 */
	protected function check_parameters_registered_in_wp( array $action ) {
		global $wp_filter;
		$registered_action = $wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ];

		$this->assertArrayHasKey( $action['callback'], $registered_action );
		$this->assertEquals( $action['callback'], $registered_action[ $action['callback'] ]['function'] );
		$this->assertEquals( $action['args'], $registered_action[ $action['callback'] ]['accepted_args'] );

		// Then remove the action.
		remove_action( $action['hook'], $action['callback'], $action['priority'] );
	}

	/**
	 * Setup the original action.
	 *
	 * @since 1.5.0
	 *
	 * @param string $id Optional. Beans ID to register. Default is 'foo'.
	 *
	 * @return array
	 */
	protected function setup_original_action( $id = 'foo' ) {
		$action = array(
			'hook'     => 'beans_hook',
			'callback' => 'callback_beans',
			'priority' => 10,
			'args'     => 1,
		);

		$this->check_not_added( 'beans', $action['hook'] );

		// Add the original action to get us rolling.
		beans_add_action( $id, $action['hook'], $action['callback'] );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( $action );

		return $action;
	}
}
