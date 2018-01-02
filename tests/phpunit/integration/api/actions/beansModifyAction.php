<?php
/**
 * Tests for beans_modify_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use WP_UnitTestCase;

/**
 * Class Tests_BeansModifyAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyAction extends WP_UnitTestCase {

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
	 * Test beans_modify_action() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$this->assertFalse( beans_modify_action( 'foo' ) );
	}

	/**
	 * Test beans_modify_action() should register with Beans as modified, but not with WordPress.
	 */
	public function test_should_register_with_beans_as_modified_but_not_with_wp() {
		$action = array(
			'hook'     => 'foo_hook',
			'callback' => 'my_callback',
		);

		$this->check_not_added( 'foo', $action['hook'] );

		$this->assertFalse( beans_modify_action( 'foo', $action['hook'], $action['callback'] ) );

		// Now check that it was not registered in WordPress.
		$this->assertFalse( has_action( $action['hook'] ) );
		global $wp_filter;
		$this->assertFalse( array_key_exists( $action['hook'], $wp_filter ) );

		// Now check in Beans.
		$this->assertEquals( $action, _beans_get_action( 'foo', 'modified' ) );
	}

	/**
	 * Test beans_modify_action() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$action          = $this->setup_original_action();
		$modified_action = array(
			'callback' => 'my_callback',
		);
		$this->assertTrue( beans_modify_action( 'beans', null, $modified_action['callback'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( array_merge( $action, $modified_action ) );
	}

	/**
	 * Test beans_modify_action() should modify the registered action's priority level.
	 */
	public function test_should_modify_the_action_priority() {
		$action          = $this->setup_original_action();
		$modified_action = array(
			'priority' => 20,
		);
		$this->assertTrue( beans_modify_action( 'beans', null, null, $modified_action['priority'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( array_merge( $action, $modified_action ) );
	}

	/**
	 * Test beans_modify_action() should modify the registered action's number of arguments.
	 */
	public function test_should_modify_the_action_args() {
		$action          = $this->setup_original_action();
		$modified_action = array(
			'args' => 2,
		);
		$this->assertTrue( beans_modify_action( 'beans', null, null, null, $modified_action['args'] ) );
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
	 * @return array
	 */
	protected function setup_original_action() {
		$action = array(
			'hook'     => 'beans_hook',
			'callback' => 'callback_beans',
			'priority' => 10,
			'args'     => 1,
		);

		$this->check_not_added( 'beans', $action['hook'] );

		// Add the original action to get us rolling.
		beans_add_action( 'beans', $action['hook'], $action['callback'] );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( $action );

		return $action;
	}
}
