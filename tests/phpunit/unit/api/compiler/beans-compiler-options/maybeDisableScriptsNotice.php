<?php
/**
 * Tests the maybe_disable_scripts_notice() method of _Beans_Compiler_Options.
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
 * Class Tests_BeansCompilerOptions_MaybeDisableScriptsNotice
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_MaybeDisableScriptsNotice extends Compiler_Options_Test_Case {

	/**
	 * Test _Beans_Compiler_Options::maybe_disable_scripts_notice() should not render when compile scripts not an option.
	 */
	public function test_should_not_render_when_compile_scripts_not_an_option() {
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_scripts' )
			->andReturnNull();
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->never();
		Monkey\Functions\expect( 'esc_html_e' )->never();

		ob_start();
		( new _Beans_Compiler_Options() )->maybe_disable_scripts_notice();

		$this->assertEmpty( ob_get_clean() );
	}

	/**
	 * Test _Beans_Compiler_Options::maybe_disable_scripts_notice() should not render when Compiler is not in dev mode.
	 */
	public function test_should_not_render_when_compiler_not_in_dev_mode() {
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_scripts' )
			->andReturn( 1 );
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )
			->once()
			->andReturn( false );
		Monkey\Functions\expect( 'esc_html_e' )->never();

		ob_start();
		( new _Beans_Compiler_Options() )->maybe_disable_scripts_notice();

		$this->assertEmpty( ob_get_clean() );
	}

	/**
	 * Test _Beans_Compiler_Options::maybe_disable_scripts_notice() should render when compile scripts is selected and Compiler
	 * is in dev mode.
	 */
	public function test_should_render_when_compile_scripts_selected_and_compiler_in_dev_mode() {
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_scripts' )
			->andReturn( 1 );
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )
			->once()
			->andReturn( true );

		ob_start();
		( new _Beans_Compiler_Options() )->maybe_disable_scripts_notice();
		$actual = ob_get_clean();

		$expected = <<<EOB
<br />		
<span style="color: #d85030;">Scripts are not compiled in development mode.</span>
EOB;
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}
}
