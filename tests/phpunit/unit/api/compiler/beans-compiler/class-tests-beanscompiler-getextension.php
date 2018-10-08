<?php
/**
 * Tests for the get_extension() method of Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_GetExtension
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_GetExtension extends Compiler_Test_Case {

	/**
	 * Test Beans_Compiler::get_extension() should return "css" when the type is "style".
	 */
	public function test_should_return_css_when_style() {
		$compiler = $this->create_compiler( [ 'type' => 'style' ] );

		$this->assertSame( 'css', $compiler->get_extension() );
	}

	/**
	 * Test Beans_Compiler::get_extension() should return "js" when the type is "script".
	 */
	public function test_should_return_js_when_script() {
		$compiler = $this->create_compiler( [ 'type' => 'script' ] );

		$this->assertSame( 'js', $compiler->get_extension() );
	}

	/**
	 * Test Beans_Compiler::get_extension() should return null when the type is invalid.
	 */
	public function test_should_return_null_when_invalid_type() {
		$compiler = $this->create_compiler( [ 'type' => 'invalid' ] );
		$this->assertNull( $compiler->get_extension() );

		$compiler = $this->create_compiler( [ 'type' => null ] );
		$this->assertNull( $compiler->get_extension() );

		$compiler = $this->create_compiler( [ 'type' => false ] );
		$this->assertNull( $compiler->get_extension() );

		$compiler = $this->create_compiler( [] );
		$this->assertNull( $compiler->get_extension() );
	}
}
