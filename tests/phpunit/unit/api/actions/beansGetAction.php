<?php
/**
 * Tests for _beans_get_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansGetAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGetAction extends Test_Case {

	/**
	 * Available action statuses.
	 *
	 * @var array
	 */
	protected $action_status;

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->action_status = array( 'added', 'modified', 'removed', 'replaced' );
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
	 * Test _beans_get_action() should return false when the status is empty, i.e. no actions are registered.
	 */
	public function test_should_return_false_when_status_is_empty() {

		foreach ( $this->action_status as $action_status ) {
			$this->assertFalse( _beans_get_action( 'foo', $action_status ) );
		}
	}

	/**
	 * Test _beans_get_action() should return false when the action is not registered.
	 */
	public function test_should_return_false_when_action_is_not_registered() {
		global $_beans_registered_actions;
		// @codingStandardsIgnoreLine - WordPress.WP.AlternativeFunctions.json_encode_json_encode as json_encode is appropriate here for testing.
		$_beans_registered_actions['added']['foo'] = json_encode( array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		) );

		$this->assertFalse( _beans_get_action( 'foobar', 'added' ) );

		// Make sure we get false on the other statuses.
		foreach ( $this->action_status as $action_status ) {

			// Skip the 'added' status.
			if ( 'added' === $action_status ) {
				continue;
			}

			$this->assertFalse( _beans_get_action( 'foo', $action_status ) );
		}
	}

	/**
	 * Test _beans_get_action() should return the action's configuration when it's registered.
	 */
	public function test_should_return_action_when_registered() {
		global $_beans_registered_actions;

		$action_configuration = array(
			'hook'     => 'foo',
			'callback' => 'callback',
			'priority' => 10,
			'args'     => 1,
		);

		foreach ( $this->action_status as $action_status ) {
			$_beans_registered_actions[ $action_status ]['foo'] = json_encode( $action_configuration ); // @codingStandardsIgnoreLine - WordPress.WP.AlternativeFunctions.json_encode_json_encode as json_encode is appropriate here for testing.
			$this->assertEquals( $action_configuration, _beans_get_action( 'foo', $action_status ) );
		}
	}
}
