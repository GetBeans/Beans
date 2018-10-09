<?php
/**
 * Tests for beans_flush_compiler()
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansFlushCompiler
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansFlushCompiler extends Compiler_Test_Case {

	/**
	 * Test beans_flush_compiler() should bail out when the directory does not exist.
	 */
	public function test_should_return_absolute_path_to_compiler_folder() {
		Monkey\Functions\expect( 'beans_remove_dir' )->never();

		$this->assertNull( beans_flush_compiler( 'beans' ) );
	}

	/**
	 * Test beans_flush_compiler() should remove cached CSS file.
	 */
	public function test_should_remove_cached_css_file() {
		$this->overwrite_virtual_filesystem();

		// Check that both files exist before we start.
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );

		// Run it.
		$this->assertNull( beans_flush_compiler( 'beans', 'css' ) );

		// Check that only the CSS file was removed.
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
	}


	/**
	 * Test beans_flush_compiler() should remove cached JS file.
	 */
	public function test_should_remove_cached_js_file() {
		$this->overwrite_virtual_filesystem();

		// Check that both files exist before we start.
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );

		// Run it.
		$this->assertNull( beans_flush_compiler( 'beans', 'js' ) );

		// Check that only the JS file was removed.
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
	}

	/**
	 * Test beans_flush_compiler() should remove all cached files.
	 */
	public function test_should_remove_all_cached_files() {
		$this->overwrite_virtual_filesystem();

		// Check that both files exist before we start.
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );

		// Run it.
		$this->assertNull( beans_flush_compiler( 'beans' ) );

		// Check that the files and directory were removed.
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
		$this->assertDirectoryNotExists( vfsStream::url( 'compiled/beans/compiler/beans' ) );
		$this->assertDirectoryNotExists( vfsStream::url( 'compiled/beans/compiler/beans/' ) );
	}

	/**
	 * Overwrites the virtual filesystem for this test.
	 */
	private function overwrite_virtual_filesystem() {
		$this->mock_filesystem = vfsStream::setup(
			'compiled',
			0755,
			[
				'beans' => [
					'compiler' => [
						'beans' => [
							'1234567-9876543.css' => $this->get_compiled_css(),
							'abcd3fg-hijklmn.js'  => $this->get_compiled_js(),
						],
					],
				],
			]
		);
	}
}
