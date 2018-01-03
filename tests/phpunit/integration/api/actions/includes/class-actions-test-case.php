<?php
/**
 * Tests Case for Beans' Action API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Actions\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions\Includes;

use WP_UnitTestCase;

/**
 * Abstract Class Actions_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 */
abstract class Actions_Test_Case extends WP_UnitTestCase {

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
