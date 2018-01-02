<?php
/**
 * Tests for beans_add_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use WP_UnitTestCase;

/**
 * Class Tests_BeansAddAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansAddAction extends WP_UnitTestCase {

	/**
	 * The action.
	 *
	 * @var array
	 */
	protected $action;

	/**
	 * Setup test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->action = array(
			'hook'     => 'foo_hook',
			'callback' => 'callback_foo',
			'priority' => 10,
			'args'     => 1,
		);
	}

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
	 * Test beans_add_action() should register the action in WordPress.
	 */
	public function test_should_register_action_in_wordpress() {
		$this->check_not_added( 'foo', $this->action['hook'] );

		// Add the action.
		$this->assertTrue( beans_add_action( 'foo', $this->action['hook'], $this->action['callback'] ) );

		// Now check that it was registered in WordPress.
		$this->assertTrue( has_action( $this->action['hook'] ) );
		$this->check_parameters_registered_in_wp( $this->action );

		// Now check in Beans.
		$this->assertEquals( $this->action, _beans_get_action( 'foo', 'added' ) );
	}

	/**
	 * Test beans_add_action() should use the action configuration in "replaced" status, when it's available.
	 */
	public function test_should_use_replaced_action_when_available() {
		$this->check_not_added( 'foo', $this->action['hook'] );

		// Setup by storing in the "replaced" status.
		$replaced_action = array(
			'hook'     => $this->action['hook'],
			'callback' => 'my_callback',
			'priority' => 20,
			'args'     => 2,
		);
		_beans_set_action( 'foo', $replaced_action, 'replaced', true );

		// Add the action.
		$this->assertTrue( beans_add_action( 'foo', $this->action['hook'], $this->action['callback'] ) );

		// Now check that it was registered in WordPress.
		$this->assertTrue( has_action( $replaced_action['hook'] ) );
		$this->check_parameters_registered_in_wp( $replaced_action );

		// Now check in Beans.
		$this->assertEquals( $replaced_action, _beans_get_action( 'foo', 'added' ) );
	}

	/**
	 * Test beans_add_action() should return null when the ID is registered to the "removed" status.
	 */
	public function test_should_return_null_when_removed() {
		$this->check_not_added( 'foo', $this->action['hook'] );

		// Setup by storing in the "removed" status.
		_beans_set_action( 'foo', $this->action, 'removed', true );

		// Add the action.
		$this->assertNull( beans_add_action( 'foo', $this->action['hook'], $this->action['callback'] ) );

		// Now check that it was registered in WordPress.
		$this->assertFalse( has_action( $this->action['hook'] ) );
		global $wp_filter;
		$this->assertFalse( array_key_exists( $this->action['hook'], $wp_filter ) );

		// Now check in Beans.
		$this->assertEquals( $this->action, _beans_get_action( 'foo', 'added' ) );
	}

	/**
	 * Test beans_add_action() should merge the "modified" action configuration parameters.
	 */
	public function test_should_merge_modified_action_parameters() {
		$this->check_not_added( 'foo', $this->action['hook'] );

		// Setup by storing in the "modified" status.
		$modified_action = array(
			'hook'     => $this->action['hook'],
			'callback' => 'my_callback',
			'priority' => 20,
			'args'     => 2,
		);
		_beans_set_action( 'foo', $modified_action, 'modified', true );

		// Add the action.
		$this->assertTrue( beans_add_action( 'foo', $this->action['hook'], $this->action['callback'] ) );

		// Now check that it was registered in WordPress.
		$this->assertTrue( has_action( $modified_action['hook'] ) );
		$this->check_parameters_registered_in_wp( $modified_action );

		// Now check in Beans.
		$this->assertEquals( $this->action, _beans_get_action( 'foo', 'added' ) );
	}

	/**
	 * Check that is not registered first.
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
	 * Test that the right parameters are registered in WordPress.
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
}
