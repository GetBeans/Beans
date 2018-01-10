<?php
/**
 * Tests for beans_modify_action()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyAction
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyAction extends Actions_Test_Case {

	/**
	 * Test beans_modify_action() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$this->assertFalse( beans_modify_action( 'foo' ) );
	}

	/**
	 * Test beans_modify_action_callback() should return false when there's nothing to modify,
	 * i.e. no arguments passed.
	 */
	public function test_should_return_false_when_nothing_to_modify() {
		$this->setup_original_action();
		$this->assertFalse( beans_modify_action( 'foo' ) );

		$this->setup_original_action( 'beans' );
		$this->assertFalse( beans_modify_action( 'beans' ) );
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
		$action          = $this->setup_original_action( 'beans' );
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
		$action          = $this->setup_original_action( 'beans' );
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
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'args' => 2,
		);
		$this->assertTrue( beans_modify_action( 'beans', null, null, null, $modified_action['args'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( array_merge( $action, $modified_action ) );
	}
}
