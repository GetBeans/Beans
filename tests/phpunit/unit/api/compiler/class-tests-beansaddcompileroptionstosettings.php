<?php
/**
 * Tests for beans_add_compiler_options_to_settings()
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans_Compiler_Options;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Options_Test_Case;

require_once __DIR__ . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansAddCompilerOptionsToSettings
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansAddCompilerOptionsToSettings extends Compiler_Options_Test_Case {

	/**
	 * Test beans_add_compiler_options_to_settings() should return instance of Beans_Compiler_Options().
	 */
	public function test_should_return_instance_of_beans_compiler_options() {
		$this->assertInstanceOf( Beans_Compiler_Options::class, beans_add_compiler_options_to_settings() );
	}
}
