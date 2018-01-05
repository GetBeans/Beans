<?php
/**
 * Tests for beans_add_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansAddAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansAddAction extends Actions_Test_Case {

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
	 * Test beans_add_action() should return false when the ID is registered to the "removed" status.
	 */
	public function test_should_return_false_when_removed() {
		$this->check_not_added( 'foo', $this->action['hook'] );

		// Setup by storing in the "removed" status.
		_beans_set_action( 'foo', $this->action, 'removed', true );

		// Add the action.
		$this->assertFalse( beans_add_action( 'foo', $this->action['hook'], $this->action['callback'] ) );

		// Now check that it was not registered in WordPress.
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
}
