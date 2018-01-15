<?php
/**
 * Tests Case for Beans' Action API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class Actions_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 */
abstract class Actions_Test_Case extends Test_Case {

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
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_actions = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-actions.php';
		static::$test_ids     = array_keys( static::$test_actions );

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

		$this->remove_test_actions();
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

	/**
	 * Simulate going to the post and loading in the template and fragments.
	 */
	protected function go_to_post() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] );
		}
	}

	/**
	 * Remove the test actions.
	 */
	protected function remove_test_actions() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			_beans_unset_action( $beans_id, 'added' );
			remove_action( $action['hook'], $action['callback'], $action['priority'] );
		}
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
		$container = Monkey\Container::instance();
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->assertTrue(
			$container->hookStorage()->isHookAdded(
				Monkey\Hook\HookStorage::ACTIONS,
				$action['hook'],
				$action['callback']
			)
		);

		// Then remove the action.
		if ( $remove_action ) {
			remove_action( $action['hook'], $action['callback'], $action['priority'] );
		}
	}
}
