<?php
/**
 * Tests for beans_add_action()
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
 * Class Tests_BeansAddAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
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
	protected function setUp() {
		parent::setUp();

		$this->action = array(
			'hook'     => 'foo_hook',
			'callback' => 'callback_foo',
			'priority' => 10,
			'args'     => 1,
		);
	}

	/**
	 * Test beans_add_action() should add (register) the action.
	 */
	public function test_should_add_the_action() {
		$this->check_not_added( 'foo', $this->action['hook'] );

		// Add the action.
		$this->assertTrue( beans_add_action( 'foo', $this->action['hook'], $this->action['callback'] ) );

		// Now check.
		$this->assertTrue( has_action( $this->action['hook'] ) );
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

		// Now check.
		$this->assertTrue( has_action( $replaced_action['hook'] ) );
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

		// Now check.
		$this->assertFalse( has_action( 'foo_hook' ) );
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

		// Check that the original action is registered with Beans.
		$this->assertEquals( $this->action, _beans_get_action( 'foo', 'added' ) );

		// Finally, check that the "modified" action is registered via add_action.
		$container = Monkey\Container::instance();
		$this->assertTrue( $container->hookStorage()->isHookAdded( Monkey\Hook\HookStorage::ACTIONS, 'foo_hook', 'my_callback' ) );
	}
}
