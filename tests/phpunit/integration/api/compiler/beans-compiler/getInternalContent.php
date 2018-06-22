<?php
/**
 * Tests for the get_internal_content() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_GetInternalContent
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_GetInternalContent extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::get_internal_content() should return false when fragment is empty.
	 */
	public function test_should_return_false_when_fragment_is_empty() {
		$compiler = new _Beans_Compiler( [] );

		// Run the test.
		$this->assertfalse( $compiler->get_internal_content( '' ) );
	}

	/**
	 * Test _Beans_Compiler::get_internal_content() should return false when the file does not exist.
	 */
	public function test_should_return_false_when_file_does_not_exist() {
		// Set up the compiler.
		$fragment = vfsStream::url( 'compiled/fixtures/' ) . 'invalid-file.js';
		$compiler = new _Beans_Compiler( [
			'fragments' => [ $fragment ],
		] );
		$this->set_current_fragment( $compiler, $fragment );

		// Run the test.
		$this->assertfalse( $compiler->get_internal_content( $fragment ) );
	}

	/**
	 * Test _Beans_Compiler::get_internal_content() should return a fragment's contents.
	 */
	public function test_should_return_fragment_contents() {
		// Set up the compiler.
		$fragment = vfsStream::url( 'compiled/fixtures/test.less' );
		$compiler = new _Beans_Compiler( [
			'fragments' => [ $fragment ],
		] );
		$this->set_current_fragment( $compiler, $fragment );

		// Set the WP Filesystem.
		add_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
		$compiler->filesystem();
		$this->assertInstanceOf( 'WP_Filesystem_Direct', $GLOBALS['wp_filesystem'] );

		// Run the tests.
		$contents = $compiler->get_internal_content( $fragment );
		$this->assertContains( 'body {', $contents );
		$this->assertContains( 'color: @body-color;', $contents );

		// Clean up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}
}
