<?php
/**
 * Tests for beans_compile_js_fragments()
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;

require_once __DIR__ . '/includes/class-compiler-test-case.php';

/**
 * Class Test_BeansCompileJsFragments
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompileJsFragments extends Compiler_Test_Case {

	/**
	 * Test beans_compile_js_fragments() should return false when no fragments are given.
	 */
	public function test_should_return_false_when_no_fragments_given() {
		$this->assertFalse( beans_compile_js_fragments( 'foo', '' ) );
		$this->assertFalse( beans_compile_js_fragments( 'foo', [] ) );
		$this->assertFalse(
			beans_compile_js_fragments(
				'foo',
				[],
				[
					'dependencies' => [],
					'in_footer'    => true,
				]
			)
		);
	}
}
