<?php
/**
 * Test Case for Beans' Action API "replace action" integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Actions\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions\Includes;

/**
 * Abstract Class Replace_Actions_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Actions\Includes
 */
abstract class Replace_Action_Test_Case extends Actions_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		$this->reset_beans_registry = false;

		parent::setUp();

		// Just in case the original action is already registered, remove it.
		$this->remove_original_action();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {

		// Reset and restore.
		foreach ( static::$test_actions as $beans_id => $action ) {
			// Reset Beans.
			_beans_unset_action( $beans_id, 'modified' );
			_beans_unset_action( $beans_id, 'replaced' );
			_beans_unset_action( $beans_id, 'added' );

			// Restore the original action.
			beans_add_smart_action( $action['hook'], $action['callback'], $action['priority'], $action['args'] );
		}

		parent::tearDown();
	}

	/**
	 * Store the original action and then remove it.  These steps allow us to set up an
	 * initial test where the action is not registered.  Then when we're doing testing, we can
	 * restore it.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	private function remove_original_action() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			_beans_unset_action( $beans_id, 'added' );

			if ( has_action( $action['hook'], $action['callback'] ) ) {
				remove_action( $action['hook'], $action['callback'], $action['priority'], $action['args'] );
			}
		}
	}

	/**
	 * Merge the action's configuration with the defaults.
	 *
	 * @since 1.5.0
	 *
	 * @param array $action The action to merge.
	 *
	 * @return array
	 */
	protected function merge_action_with_defaults( array $action ) {
		return array_merge(
			[
				'hook'     => null,
				'callback' => null,
				'priority' => null,
				'args'     => null,
			],
			$action
		);
	}

	/**
	 * Check that the "replaced" action has been stored in Beans.
	 *
	 * @since 1.5.0
	 *
	 * @param string $beans_id        The Beans unique ID.
	 * @param array  $replaced_action The "replaced" action's configuration.
	 *
	 * @return void
	 */
	protected function check_stored_in_beans( $beans_id, array $replaced_action ) {
		$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'replaced' ) );
		$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
	}
}
