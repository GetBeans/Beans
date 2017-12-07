<?php
/**
 * Tests for _beans_unique_action_id()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansUniqueActionId
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansUniqueActionId extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
		require_once __DIR__ . '/stubs/class-action-stub.php';
	}

	/**
	 * Test _beans_unique_action_id() should return when string given.
	 */
	public function test_should_return_when_string_given() {
		$this->assertEquals( "I'm a string", _beans_unique_action_id( "I'm a string" ) );
		$this->assertEquals( 'I\'m a string', _beans_unique_action_id( 'I\'m a string' ) );
		$this->assertEquals( 'foo', _beans_unique_action_id( 'foo' ) );
		$this->assertEquals( 'trim', _beans_unique_action_id( 'trim' ) );
		$this->assertEquals( '__return_false', _beans_unique_action_id( '__return_false' ) );
		$this->assertEquals( __NAMESPACE__ . '\foo', _beans_unique_action_id( __NAMESPACE__ . '\foo' ) );
		$this->assertEquals(
			'Tests_BeansUniqueActionId::test_should_return_when_string',
			_beans_unique_action_id( 'Tests_BeansUniqueActionId::test_should_return_when_string' )
		);
	}

	/**
	 * Test _beans_unique_action_id() should convert static method.
	 */
	public function test_should_convert_static_method() {
		$this->assertEquals(
			'Foo::bar',
			_beans_unique_action_id( array( 'Foo', 'bar' ) )
		);
		$this->assertEquals(
			'Tests_BeansUniqueActionId::test_should_return_when_string',
			_beans_unique_action_id( array( 'Tests_BeansUniqueActionId', 'test_should_return_when_string' ) )
		);

		$stub_classname = __NAMESPACE__ . '\Actions_Stub';
		$this->assertEquals(
			$stub_classname . '::dummy_static_method',
			_beans_unique_action_id( array( $stub_classname, 'dummy_static_method' ) )
		);
		$this->assertEquals(
			$stub_classname . '::dummy_static_method',
			_beans_unique_action_id( $stub_classname . '::dummy_static_method' )
		);

		$stub = new Actions_Stub();
		$this->assertNotEquals(
			$stub_classname . '::dummy_static_method',
			_beans_unique_action_id( array( $stub, 'dummy_static_method' ) )
		);
		$this->assertStringEndsWith(
			'dummy_static_method',
			_beans_unique_action_id( array( $stub, 'dummy_static_method' ) )
		);
		$this->assertEquals(
			spl_object_hash( $stub ) . 'dummy_static_method',
			_beans_unique_action_id( array( $stub, 'dummy_static_method' ) )
		);
	}

	/**
	 * Test _beans_unique_action_id() should convert object.
	 */
	public function test_should_convert_object() {
		// 2 different objects should have unique IDs.
		$this->assertNotEquals( new \stdClass(), _beans_unique_action_id( new \stdClass() ) );
		$this->assertNotEquals( new Actions_Stub(), _beans_unique_action_id( new Actions_Stub() ) );

		$stub = new Actions_Stub();
		$this->assertEquals( spl_object_hash( $stub ), _beans_unique_action_id( $stub ) );
		$this->assertEquals(
			spl_object_hash( $stub ) . 'dummy_method',
			_beans_unique_action_id( array( $stub, 'dummy_method' ) )
		);
	}

	/**
	 * Test _beans_unique_action_id() should convert closure.
	 */
	public function test_should_convert_closure() {
		$closure = function( $dummy_arg ) {
			return $dummy_arg;
		};

		$this->assertEquals( spl_object_hash( $closure ), _beans_unique_action_id( $closure ) );
		$this->assertNotEquals( spl_object_hash( function() {
			// Nothing here.
		} ), _beans_unique_action_id( $closure ) );
	}
}
