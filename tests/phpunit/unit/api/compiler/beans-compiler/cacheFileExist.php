<?php
/**
 * Tests for the cache_file_exist() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_CacheFileExist
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_CacheFileExist extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::cache_file_exist() should return false when the filename has not been generated.
	 */
	public function test_should_return_false_when_filename_not_generated() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$compiler = $this->create_compiler( [
			'id'        => 'test',
			'type'      => 'script',
			'fragments' => [ $fragment ],
		] );

		// Run cache_file_exist().
		$this->assertFalse( $compiler->cache_file_exist() );
	}

	/**
	 * Test _Beans_Compiler::cache_file_exist() should return false when the cached file does not exist.
	 */
	public function test_should_return_false_when_file_does_not_exist() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$compiler = $this->create_compiler( [
			'id'        => 'test-script',
			'type'      => 'script',
			'fragments' => [ $fragment ],
		] );

		// Mock the compiler's property.
		$filename = '9a71ddb-b8d5d01.js';
		$this->set_reflective_property( $filename, 'filename', $compiler );

		// Run the tests.
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/test-script/' . $filename ) );
		$this->assertFalse( $compiler->cache_file_exist() );
	}

	/**
	 * Test _Beans_Compiler::cache_file_exist() should return true when the cached file does exist.
	 */
	public function test_should_return_true_when_file_exists() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$compiler = $this->create_compiler( [
			'id'        => 'test-script',
			'type'      => 'script',
			'fragments' => [ $fragment ],
		] );

		// Mock the compiler's property.
		$filename = '9a71ddb-b8d5d01.js';
		$this->set_reflective_property( $filename, 'filename', $compiler );

		// Add the cached file to the virtual filesystem.
		$cached_file = $this->create_virtual_file( 'test-script', $filename, $this->get_compiled_js() );

		// Run the tests.
		$this->assertFileExists( $cached_file );
		$this->assertTrue( $compiler->cache_file_exist() );
	}
}
