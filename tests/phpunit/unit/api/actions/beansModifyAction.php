<?php
/**
 * Tests for beans_modify_action()
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
 * Class Tests_BeansModifyAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansModifyAction extends Actions_Test_Case {

	/**
	 * Test beans_modify_action() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$ids = array(
			'foo',
			'bar',
			'baz',
			'beans',
		);
		foreach ( $ids as $id ) {
			$this->assertFalse( _beans_get_action( $id, 'modified' ) );

			$modified_action = array(
				'hook'     => "{$id}_hook",
				'callback' => "{$id}_callback",
			);
			$this->assertFalse( beans_modify_action( $id, $modified_action['hook'], $modified_action['callback'] ) );
			$this->assertEquals( $modified_action, _beans_get_action( $id, 'modified' ) );
			$this->assertFalse( has_action( $modified_action['hook'] ) );
		}
	}

	/**
	 * Test beans_modify_action_callback() should return false when there's nothing to modify,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_modify() {
		$ids = array(
			'foo',
			'bar',
			'baz',
			'beans',
		);
		foreach ( $ids as $id ) {
			$this->assertFalse( beans_modify_action( $id ) );

			// Check after we setup the original action...just to make sure.
			$this->setup_original_action( $id );
			$this->assertFalse( beans_modify_action( $id ) );
		}
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

		// Check that it did register with Beans.
		$this->assertEquals( $action, _beans_get_action( 'foo', 'modified' ) );

		// Check that it did not add the action.
		$this->assertFalse( has_action( $action['hook'] ) );
	}

	/**
	 * Test beans_modify_action() should modify the registered action's callback.
	 */
	public function test_should_modify_the_action_callback() {
		$container       = Monkey\Container::instance();
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'callback' => 'my_callback',
		);
		$this->assertTrue( beans_modify_action( 'beans', null, $modified_action['callback'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->assertTrue( $container->hookStorage()->isHookAdded( Monkey\Hook\HookStorage::ACTIONS, $action['hook'], $modified_action['callback'] ) );
	}

	/**
	 * Test beans_modify_action() should modify the registered action's priority level.
	 */
	public function test_should_modify_the_action_priority() {
		$container       = Monkey\Container::instance();
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'priority' => 20,
		);
		$this->assertTrue( beans_modify_action( 'beans', null, null, $modified_action['priority'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->assertTrue( $container->hookStorage()->isHookAdded( Monkey\Hook\HookStorage::ACTIONS, $action['hook'], $action['callback'] ) );
	}

	/**
	 * Test beans_modify_action() should modify the registered action's number of arguments.
	 */
	public function test_should_modify_the_action_args() {
		$container       = Monkey\Container::instance();
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'args' => 2,
		);
		$this->assertTrue( beans_modify_action( 'beans', null, null, null, $modified_action['args'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->assertTrue( $container->hookStorage()->isHookAdded( Monkey\Hook\HookStorage::ACTIONS, $action['hook'], $action['callback'] ) );
	}
}
