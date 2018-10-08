<?php
/**
 * Tests for the register() method of _Beans_Compiler_Options.
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
 * Class Tests_BeansCompilerOptions_Register
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_Register extends Compiler_Options_Test_Case {

	/**
	 * Array of fields.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->fields = require BEANS_THEME_DIR . '/lib/api/compiler/config/fields.php';
	}

	/**
	 * Test _Beans_Compiler_Options::register() should register only the flush button when the styles and scripts are
	 * not supported.
	 */
	public function test_should_register_only_flush_button_when_styles_and_scripts_not_supported() {
		unset( $this->fields['beans_compile_all_styles'] );
		unset( $this->fields['beans_compile_all_scripts_group'] );

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( false )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$this->fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register the styles options when not supported.
	 */
	public function test_should_not_register_styles_options_when_not_supported() {
		unset( $this->fields['beans_compile_all_styles'] );

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( false )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$this->fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register the scripts options when not supported.
	 */
	public function test_should_not_register_scripts_options_when_not_supported() {
		unset( $this->fields['beans_compile_all_scripts_group'] );

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( true )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$this->fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should register all options when styles and scripts are supported.
	 */
	public function test_should_register_all_options_when_styles_and_scripts_supported() {
		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( true )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$this->fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}
}
