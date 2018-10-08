<?php
/**
 * Tests for the render_styles_not_compiled_notice() method of _Beans_Compiler_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use _Beans_Compiler_Options;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansCompilerOptions_RenderStylesNotCompiledNotice
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_RenderStylesNotCompiledNotice extends Compiler_Options_Test_Case {

	/**
	 * Test _Beans_Compiler_Options::render_styles_not_compiled_notice() should not render when Compiler is not in dev
	 * mode.
	 */
	public function test_should_not_render_when_compiler_not_in_dev_mode() {
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )
			->once()
			->andReturn( false );
		Monkey\Functions\expect( 'get_option' )->never();
		Monkey\Functions\expect( 'esc_html_e' )->never();

		$this->assertNull( ( new _Beans_Compiler_Options() )->render_styles_not_compiled_notice() );
	}

	/**
	 * Test _Beans_Compiler_Options::render_styles_not_compiled_notice() should not render when compile styles is not
	 * an option.
	 */
	public function test_should_not_render_when_compile_styles_not_an_option() {
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )
			->once()
			->andReturn( true );
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_styles' )
			->andReturnNull();
		Monkey\Functions\expect( 'esc_html_e' )->never();

		$this->assertNull( ( new _Beans_Compiler_Options() )->render_styles_not_compiled_notice() );
	}

	/**
	 * Test _Beans_Compiler_Options::render_styles_not_compiled_notice() should render when compile styles is selected
	 * and Compiler is in dev mode.
	 */
	public function test_should_render_when_compile_styles_selected_and_compiler_in_dev_mode() {
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )
			->once()
			->andReturn( true );
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_styles' )
			->andReturn( 1 );

		ob_start();
		( new _Beans_Compiler_Options() )->render_styles_not_compiled_notice();
		$actual = ob_get_clean();

		$expected = <<<EOB
<br />
<span style="color: #d85030;">Styles are not compiled in development mode.</span>
EOB;
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}
}
