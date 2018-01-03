<?php
/**
 * Tests for beans_modify_action_arguments()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use WP_UnitTestCase;

/**
 * Class Tests_BeansModifyActionArguments
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyActionArguments extends WP_UnitTestCase {

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
	 * Test beans_modify_action_arguments() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$ids = array(
			'foo'   => null,
			'bar'   => 0,
			'baz'   => 1,
			'beans' => '3',
		);
		foreach ( $ids as $id => $number_of_args ) {
			$this->assertFalse( beans_modify_action_arguments( $id, $number_of_args ) );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should return false when new args is a non-integer value.
	 */
	public function test_should_return_false_when_args_is_non_integer() {
		$ids = array(
			'foo'   => null,
			'bar'   => array( 10 ),
			'baz'   => false,
			'beans' => '',
		);
		foreach ( $ids as $id => $number_of_args ) {
			$action = $this->setup_original_action( $id, true );

			$this->assertFalse( beans_modify_action_arguments( $id, $number_of_args ) );
			$this->assertTrue( has_action( $action['hook'] ) );

			// Check that the parameters did not change.
			$this->check_parameters_registered_in_wp( $action );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the action's "args" when the new one is zero.
	 */
	public function test_should_modify_action_when_args_is_zero() {
		$ids = array(
			'foo'   => 0,
			'bar'   => 0.0,
			'baz'   => '0',
			'beans' => '0.0',
		);
		foreach ( $ids as $id => $number_of_args ) {
			$action = $this->setup_original_action( $id );
			$this->assertTrue( beans_modify_action_arguments( $id, $number_of_args ) );

			// Manually change the original to the new one.  Then test that it did change in WordPress.
			$action['args'] = (int) $number_of_args;
			$this->check_parameters_registered_in_wp( $action );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the registered action's args.
	 */
	public function test_should_modify_the_action_args() {
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'args' => 3,
		);
		$this->assertTrue( beans_modify_action_arguments( 'beans', $modified_action['args'] ) );
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
	 * @param array $action        The action that should be registered.
	 * @param bool  $remove_action When true, it removes the action automatically to clean up this test.
	 *
	 * @return void
	 */
	protected function check_parameters_registered_in_wp( array $action, $remove_action = true ) {
		global $wp_filter;
		$registered_action = $wp_filter[ $action['hook'] ]->callbacks[ $action['priority'] ];

		$this->assertArrayHasKey( $action['callback'], $registered_action );
		$this->assertEquals( $action['callback'], $registered_action[ $action['callback'] ]['function'] );
		$this->assertEquals( $action['args'], $registered_action[ $action['callback'] ]['accepted_args'] );

		// Then remove the action.
		if ( $remove_action ) {
			remove_action( $action['hook'], $action['callback'], $action['priority'] );
		}
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
			'hook'     => "{$id}_hook",
			'callback' => "callback_{$id}",
			'priority' => 10,
			'args'     => 1,
		);

		$this->check_not_added( $id, $action['hook'] );

		// Add the original action to get us rolling.
		beans_add_action( $id, $action['hook'], $action['callback'] );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( $action, false );

		return $action;
	}
}
