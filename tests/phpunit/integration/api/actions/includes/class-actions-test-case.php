<?php
/**
 * Test Case for Beans' Action API integration tests.
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
	 * When true, reset $_beans_registered_actions at tear down.
	 *
	 * @var bool
	 */
	protected $reset_beans_registry = true;

	/**
	 * An array of actions to test.
	 *
	 * @var array
	 */
	protected static $test_actions;

	/**
	 * An array of Beans' IDs for our test actions.
	 *
	 * @var array
	 */
	protected static $test_ids;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_actions = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-actions.php';
		static::$test_ids     = array_keys( static::$test_actions );
	}

	/**
	 * Tear down the test before we exit this class.
	 */
	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();

		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);

		// Remove the test actions.
		foreach ( static::$test_actions as $beans_id => $action ) {
			remove_action( $action['hook'], $action['callback'], $action['priority'] );
		}

		static::$test_actions = null;
		static::$test_ids     = null;
	}

	/**
	 * Reset the test fixture.
	 */
	public function tearDown() {
		parent::tearDown();

		if ( false === $this->reset_beans_registry ) {
			return;
		}

		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);
	}

	/**
	 * Restore the original action.
	 *
	 * @since 1.5.0
	 *
	 * @param string $beans_id The Beans unique ID.
	 *
	 * @return void
	 */
	protected function restore_original( $beans_id ) {
		$action = static::$test_actions[ $beans_id ];

		_beans_unset_action( $beans_id, 'added' );

		beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] );
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
	 * Check that the action has been registered in WordPress.
	 *
	 * @since 1.5.0
	 *
	 * @param string $hook          The event's name (hook) that is registered in WordPress.
	 * @param array  $action        The action to be checked.
	 * @param bool   $remove_action When true, it removes the action automatically to clean up this test.
	 *
	 * @return void
	 */
	protected function check_registered_in_wp( $hook, array $action, $remove_action = true ) {
		$this->assertTrue( has_action( $hook, $action['callback'] ) !== false );
		$this->check_parameters_registered_in_wp( $action, $remove_action );
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

	/**
	 * Simulate going to the post and loading in the template and fragments.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	protected function go_to_post() {

		/**
		 * Restore the actions. Why? The file loads once and initially adds the actions. But then we remove them
		 * during our tests.
		 */
		foreach ( static::$test_ids as $beans_id ) {
			$this->restore_original( $beans_id );
		}

		$post_id = self::factory()->post->create( array( 'post_title' => 'Hello Beans' ) );
		$this->go_to( get_permalink( $post_id ) );
		do_action( 'template_redirect' ); // @codingStandardsIgnoreLine
	}
}
