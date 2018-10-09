<?php
/**
 * Tests for the cache_file() method of Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Mockery;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_CacheFile
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_CacheFile extends Compiler_Test_Case {

	/**
	 * Test Beans_Compiler::cache_file() should not create the file when the filename is empty.
	 */
	public function test_should_not_create_the_file_when_filename_empty() {
		$compiler = $this->create_compiler(
			[
				'id'           => 'test-script',
				'type'         => 'script',
				'fragments'    => [ vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ],
				'dependencies' => [ 'jquery' ],
				'in_footer'    => true,
				'minify_js'    => true,
			]
		);

		// Run the tests.
		$this->assertFalse( $compiler->cache_file() );

		$this->set_reflective_property( null, 'filename', $compiler );
		$this->assertFalse( $compiler->cache_file() );
	}

	/**
	 * Test Beans_Compiler::cache_file() should create the compiled jQuery file.
	 */
	public function test_should_create_compiled_jquery_file() {
		$compiler = $this->create_compiler(
			[
				'id'           => 'test-jquery',
				'type'         => 'script',
				'fragments'    => [ vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ],
				'dependencies' => [ 'jquery' ],
				'in_footer'    => true,
				'minify_js'    => true,
			]
		);

		// Mock the compiler's properties.
		$compiled_content = $this->get_compiled_jquery();
		$filename         = '099c7b1-68d31c8.js';
		$cached_file      = vfsStream::url( 'compiled/beans/compiler/test-jquery/' . $filename );
		$this->set_reflective_property( $compiled_content, 'compiled_content', $compiler );
		$this->set_reflective_property( $filename, 'filename', $compiler );

		// Mock the filesystem.
		$this->mock_creating_file( 'test-jquery', $filename, $cached_file, $compiled_content );

		// Run the tests.
		$this->assertFileNotExists( $cached_file );
		$this->assertTrue( $compiler->cache_file() );
		$this->assertFileExists( $cached_file );
	}

	/**
	 * Test Beans_Compiler::cache_file() should create the compiled JavaScript file.
	 */
	public function test_should_create_compiled_javascript_file() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test-js',
				'type'      => 'script',
				'fragments' => [ vfsStream::url( 'compiled/fixtures/my-game-clock.js' ) ],
				'in_footer' => true,
				'minify_js' => true,
			]
		);

		// Mock the compiler's properties.
		$compiled_content = $this->get_compiled_jquery();
		$filename         = '9a71ddb-b8d5d01.js';
		$cached_file      = vfsStream::url( 'compiled/beans/compiler/test-js/' . $filename );
		$this->set_reflective_property( $compiled_content, 'compiled_content', $compiler );
		$this->set_reflective_property( $filename, 'filename', $compiler );

		// Mock the filesystem.
		$this->mock_creating_file( 'test-js', $filename, $cached_file, $compiled_content );

		// Run the tests.
		$this->assertFileNotExists( $cached_file );
		$this->assertTrue( $compiler->cache_file() );
		$this->assertFileExists( $cached_file );
	}

	/**
	 * Test Beans_Compiler::cache_file() should create the compiled CSS file.
	 */
	public function test_should_create_compiled_css_file() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test-css',
				'type'      => 'style',
				'format'    => 'css',
				'fragments' => [ vfsStream::url( 'compiled/fixtures/style.css' ) ],
			]
		);

		// Mock the compiler's properties.
		$compiled_content = $this->get_compiled_css();
		$filename         = '83b8fbe-3ff95a6.css';
		$cached_file      = vfsStream::url( 'compiled/beans/compiler/test-css/' . $filename );
		$this->set_reflective_property( $compiled_content, 'compiled_content', $compiler );
		$this->set_reflective_property( $filename, 'filename', $compiler );

		// Mock the filesystem.
		$this->mock_creating_file( 'test-css', $filename, $cached_file, $compiled_content );

		// Run the tests.
		$this->assertFileNotExists( $cached_file );
		$this->assertTrue( $compiler->cache_file() );
		$this->assertFileExists( $cached_file );
	}

	/**
	 * Test Beans_Compiler::cache_file() should create the compiled LESS file.
	 */
	public function test_should_create_compiled_less_file() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test-less',
				'type'      => 'style',
				'format'    => 'less',
				'fragments' => [
					vfsStream::url( 'compiled/fixtures/variables.less' ),
					vfsStream::url( 'compiled/fixtures/test.less' ),
				],
			]
		);

		// Mock the compiler's properties.
		$compiled_content = $this->get_compiled_less();
		$filename         = '033690d-9814877.css';
		$cached_file      = vfsStream::url( 'compiled/beans/compiler/test-less/' . $filename );
		$this->set_reflective_property( $compiled_content, 'compiled_content', $compiler );
		$this->set_reflective_property( $filename, 'filename', $compiler );

		// Just checking that the filename is what we expect.
		$this->assertSame( $cached_file, $compiler->get_filename() );

		// Mock the filesystem.
		$this->mock_creating_file( 'test-less', $filename, $cached_file, $compiled_content );

		// Run the tests.
		$this->assertFileNotExists( $cached_file );
		$this->assertTrue( $compiler->cache_file() );
		$this->assertFileExists( $cached_file );
	}

	/**
	 * Mock creating the file.
	 *
	 * @since 1.5.0
	 *
	 * @param string $folder_name      Name of the folder to create, which is the configuration's ID.
	 * @param string $filename         File's name.
	 * @param string $cached_file      Cached file's name.
	 * @param string $compiled_content The compiled content.
	 *
	 * @return void
	 */
	private function mock_creating_file( $folder_name, $filename, $cached_file, $compiled_content ) {
		$mock = Mockery::mock( 'WP_Filesystem_Direct' );
		$mock->shouldReceive( 'put_contents' )
			->once()
			->with( $cached_file, $compiled_content, FS_CHMOD_FILE )
			->andReturnUsing(
				function( $cached_filename, $content ) use ( $folder_name, $filename ) {
						$this->create_virtual_file( $folder_name, $filename, $content );

						return true;
				}
			);
		$GLOBALS['wp_filesystem'] = $mock; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case as we are mocking the filesystem.
	}
}
