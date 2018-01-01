<?php
/**
 * Tests for _beans_is_action_valid()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansIsActionValid
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansIsActionValid extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
	}

	/**
	 * Test _beans_is_action_valid() should return false when one or more of the the required parameter(s)
	 * is(are) missing.
	 */
	public function test_should_return_false_when_parameter_missing() {
		$this->assertFalse( _beans_is_action_valid( array( 'hook' => 'foo' ) ) );
		$this->assertFalse( _beans_is_action_valid( array( 'callback' => 'cb' ) ) );

		$this->assertFalse( _beans_is_action_valid( array(
			'hook'     => 'foo',
			'callback' => 'cb',
		) ) );

		$this->assertFalse( _beans_is_action_valid( array(
			'hook'     => 'foo',
			'callback' => 'cb',
			'priority' => 10,
		) ) );

		$this->assertFalse( _beans_is_action_valid( array(
			'hook'     => 'foo',
			'callback' => 'cb',
			'args'     => 2,
		) ) );

		$this->assertFalse( _beans_is_action_valid( array(
			'hook'     => 'foo',
			'priority' => 10,
			'args'     => 2,
		) ) );

		$this->assertFalse( _beans_is_action_valid( array(
			'callback' => 'cb',
			'priority' => 10,
			'args'     => 2,
		) ) );

		$this->assertFalse( _beans_is_action_valid( array(
			'callback' => 'cb',
			'priority' => 10,
			'args'     => 2,
			'foo'      => 'bar',
		) ) );
	}

	/**
	 * Test _beans_is_action_valid() should return false when one or more of the required parameters is set to null.
	 */
	public function test_should_return_false_when_parameter_is_null() {
		$this->assertFalse( _beans_is_action_valid( array(
			'hook'     => 'foo',
			'callback' => null,
			'priority' => 10,
			'args'     => 2,
		) ) );
	}

	/**
	 * Test _beans_is_action_valid() should return true when the action's configuration is valid.
	 */
	public function test_should_return_true_when_action_is_valid() {
		$this->assertTrue( _beans_is_action_valid( array(
			'hook'     => 'foo',
			'callback' => 'cb',
			'priority' => 10,
			'args'     => 2,
		) ) );

		$this->assertTrue( _beans_is_action_valid( array(
			'hook'     => 'start_loop',
			'callback' => 'foo_callback',
			'priority' => 20,
			'args'     => 1,
		) ) );

		$object = new \stdClass();
		$this->assertTrue( _beans_is_action_valid( array(
			'hook'     => 'some_hook[_some_subhook]',
			'callback' => [ $object, 'callback_method' ],
			'priority' => 10,
			'args'     => 2,
		) ) );

		$this->assertTrue( _beans_is_action_valid( array(
			'hook'     => 'post_title',
			'callback' => 'Some_Object::some_method',
			'priority' => 50,
			'args'     => 3,
		) ) );
	}
}
