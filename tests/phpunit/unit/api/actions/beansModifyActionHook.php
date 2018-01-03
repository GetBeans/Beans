<?php
/**
 * Tests for beans_modify_action_hook()
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
 * Class Tests_BeansModifyActionHook
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyActionHook extends Actions_Test_Case {

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
}
