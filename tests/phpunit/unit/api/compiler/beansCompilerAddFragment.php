<?php
/**
 * Tests for beans_compiler_add_fragment()
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Options_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansCompilerAddFragment
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerAddFragment extends Compiler_Options_Test_Case {

	/**
	 * Test beans_compiler_add_fragment() should return false when no fragments are given.
	 */
	public function test_should_return_false_when_no_fragments_given() {
		$this->assertFalse( beans_compiler_add_fragment( 'foo', '', 'less' ) );
		$this->assertFalse( beans_compiler_add_fragment( 'foo', [], 'css' ) );
		$this->assertFalse( beans_compiler_add_fragment( 'foo', [], 'js' ) );
	}

	/**
	 * Test beans_compiler_add_fragment() should not add fragments when the format is not valid.
	 */
	public function test_should_not_add_fragments_when_format_is_not_valid() {
		global $_beans_compiler_added_fragments;

		$this->assertNull( beans_compiler_add_fragment( 'test-css', vfsStream::url( 'compiled/fixtures/style.css' ), 'style' ) );
		$this->assertArrayNotHasKey( 'style', $_beans_compiler_added_fragments );

		$this->assertNull( beans_compiler_add_fragment( 'test-js', vfsStream::url( 'compiled/fixtures/jquery.test.js' ), 'script' ) );
		$this->assertArrayNotHasKey( 'script', $_beans_compiler_added_fragments );
	}

	/**
	 * Test beans_compiler_add_fragment() should add fragments when the format is valid.
	 */
	public function test_should_add_fragments_when_format_is_valid() {
		global $_beans_compiler_added_fragments;

		$fragment = vfsStream::url( 'compiled/fixtures/style.css' );
		$this->assertNull( beans_compiler_add_fragment( 'test-css', $fragment, 'css' ) );
		$this->assertArrayHasKey( 'test-css', $_beans_compiler_added_fragments['css'] );
		$this->assertSame( [ $fragment ], $_beans_compiler_added_fragments['css']['test-css'] );

		$fragments = [
			vfsStream::url( 'compiled/fixtures/test.less' ),
			vfsStream::url( 'compiled/fixtures/variables.less' ),
		];
		$this->assertNull( beans_compiler_add_fragment( 'test-less', $fragments, 'less' ) );
		$this->assertArrayHasKey( 'test-less', $_beans_compiler_added_fragments['less'] );
		$this->assertSame( $fragments, $_beans_compiler_added_fragments['less']['test-less'] );

		$fragments = [
			vfsStream::url( 'compiled/fixtures/jquery.test.js' ),
			vfsStream::url( 'compiled/fixtures/my-game-clock.js' ),
		];
		$this->assertNull( beans_compiler_add_fragment( 'test-js', $fragments, 'js' ) );
		$this->assertArrayHasKey( 'test-js', $_beans_compiler_added_fragments['js'] );
		$this->assertSame( $fragments, $_beans_compiler_added_fragments['js']['test-js'] );
	}

	/**
	 * Test beans_compiler_add_fragment() should add fragment(s) to existing ID.
	 */
	public function test_should_add_fragment_to_existing_id() {
		global $_beans_compiler_added_fragments;

		$_beans_compiler_added_fragments['less']['test-css'] = [ 'theme/base.less' ];

		$fragments = [
			vfsStream::url( 'compiled/fixtures/test.less' ),
			vfsStream::url( 'compiled/fixtures/variables.less' ),
		];
		$this->assertNull( beans_compiler_add_fragment( 'test-less', $fragments, 'less' ) );
		$this->assertContains( $fragments[0], $_beans_compiler_added_fragments['less']['test-less'] );
		$this->assertContains( $fragments[1], $_beans_compiler_added_fragments['less']['test-less'] );
	}
}
