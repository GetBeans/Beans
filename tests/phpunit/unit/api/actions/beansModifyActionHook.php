<?php
/**
 * Tests for beans_modify_action_hook()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansModifyActionHook
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyActionHook extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Reset the test fixture.
	 */
	protected function tearDown() {
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
	 * Test beans_modify_action_hook() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$this->assertFalse( beans_modify_action_hook( 'foo', null ) );
		$this->assertFalse( beans_modify_action_hook( 'foo', 'foo_hook' ) );
		$this->assertFalse( beans_modify_action_hook( 'beans', 'beans_hook' ) );
	}

	/**
	 * Test beans_modify_action_hook() should return false when new hook is null.
	 */
	public function test_should_return_false_when_new_hook_is_null() {
		$this->setup_original_action();
		$this->assertFalse( beans_modify_action_hook( 'foo', null ) );

		$this->setup_original_action( 'beans' );
		$this->assertFalse( beans_modify_action_hook( 'beans', null ) );
	}

	/**
	 * Test beans_modify_action_hook() should register with Beans as modified, but not with WordPress.
	 */
	public function test_should_register_with_beans_as_modified_but_not_with_wp() {
		$action = array(
			'hook' => 'my_hook',
		);

		$this->check_not_added( 'foo', $action['hook'] );

		$this->assertFalse( beans_modify_action_hook( 'foo', $action['hook'] ) );

		// Check that it did register with Beans.
		$this->assertEquals( $action, _beans_get_action( 'foo', 'modified' ) );

		// Check that it did not add the action.
		$this->assertFalse( has_action( $action['hook'] ) );
	}

	/**
	 * Test beans_modify_action_hook() should modify the registered action's hook.
	 */
	public function test_should_modify_the_action_hook() {
		$container       = Monkey\Container::instance();
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'hook' => 'my_hook',
		);
		$this->assertTrue( beans_modify_action_hook( 'beans', $modified_action['hook'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );

		// Now check that it overwrote the "hook".
		$this->assertFalse( has_action( $action['hook'] ) );
		$this->assertTrue( has_action( $modified_action['hook'] ) );
		$this->assertTrue( $container->hookStorage()->isHookAdded( Monkey\Hook\HookStorage::ACTIONS, $modified_action['hook'], $action['callback'] ) );
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
	 * Setup the original action.
	 *
	 * @since 1.5.0
	 *
	 * @param string $id Optional. Beans ID to register. Default is 'foo'.
	 *
	 * @return array
	 */
	protected function setup_original_action( $id = 'foo' ) {
		$container = Monkey\Container::instance();
		$action    = array(
			'hook'     => "{$id}_hook",
			'callback' => "callback_{$id}",
			'priority' => 10,
			'args'     => 1,
		);

		$this->check_not_added( $id, $action['hook'] );

		// Add the original action to get us rolling.
		beans_add_action( $id, $action['hook'], $action['callback'] );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->assertTrue(
			$container->hookStorage()->isHookAdded(
				Monkey\Hook\HookStorage::ACTIONS,
				$action['hook'],
				$action['callback']
			)
		);

		return $action;
	}
}
