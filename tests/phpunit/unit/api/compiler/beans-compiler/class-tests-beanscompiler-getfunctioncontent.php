<?php
/**
 * Tests for the get_function_content() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_GetFunctionContent
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_GetFunctionContent extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::get_function_content() should return false when the given fragment is not callable.
	 */
	public function test_should_return_false_when_fragment_not_callable() {
		$compiler = $this->create_compiler();

		// Run the tests.
		$this->assertfalse( $compiler->get_function_content() );

		$this->set_current_fragment( $compiler, 'function_does_not_exists' );
		$this->assertfalse( $compiler->get_function_content() );

		$this->set_current_fragment( $compiler, [ $this, 'method_does_not_exist' ] );
		$this->assertfalse( $compiler->get_function_content() );
	}

	/**
	 * Test _Beans_Compiler::get_function_content() should return content from a function.
	 */
	public function test_should_return_content_from_function() {
		$fragment = 'beans_test_get_function_content_callback';
		$compiler = $this->create_compiler();

		// Set up the function mocks.
		Monkey\Functions\expect( $fragment )->once()->andReturn( 'Beans rocks' );
		$this->set_current_fragment( $compiler, $fragment );

		// Run the test.
		$this->assertSame( 'Beans rocks', $compiler->get_function_content() );
	}

	/**
	 * Test _Beans_Compiler::get_function_content() should return content from an object's method.
	 */
	public function test_should_return_content_from_method() {
		$compiler = $this->create_compiler();

		// Set up the mock.
		$mock = Mockery::mock( 'Get_Function_Content_Mock' );
		$mock->shouldReceive( 'get_content' )
			->once()
			->andReturn( 'Beans is innovative!' );
		$this->set_current_fragment( $compiler, [ $mock, 'get_content' ] );

		// Run the test.
		$this->assertSame( 'Beans is innovative!', $compiler->get_function_content() );
	}
}
