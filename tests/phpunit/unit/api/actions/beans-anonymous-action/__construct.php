<?php
/**
 * Tests for __construct() method for _Beans_Anonymous_Actions.
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use _Beans_Anonymous_Actions;
use Beans\Framework\Tests\Unit\API\Actions\Includes\Actions_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansAnonymousActions_Construct
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   api
 * @group   api-actions
 */
class Tests_BeansAnonymousActions_Construct extends Actions_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_API_PATH . 'actions/class-beans-anonymous-action.php';
	}

	/**
	 * Test __construct() should set the callback and arguments.
	 */
	public function test_should_set_callback_and_arguments() {
		$anonymous_action = new _Beans_Anonymous_Actions( 'beans_test_do_foo', array(
			'foo_test_callback',
			array( 'foo', 'bar', 'baz' ),
		) );

		$this->assertSame( 'foo_test_callback', $anonymous_action->callback[0] );
		$this->assertSame( array( 'foo', 'bar', 'baz' ), $anonymous_action->callback[1] );
	}

	/**
	 * Test __construct() should add the action's hook.
	 */
	public function test_should_add_action_hook() {
		$anonymous_action = new _Beans_Anonymous_Actions( 'beans_test_do_foo', array(
			'foo_test_callback',
			array( 'foo' ),
		), 50, 3 );

		$this->assertTrue( has_action( 'beans_test_do_foo', array( $anonymous_action, 'callback' ) ) );
	}
}
