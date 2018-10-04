<?php
/**
 * Tests for beans_compile_js_fragments()
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompileJsFragments
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompileJsFragments extends Compiler_Test_Case {

	/**
	 * Test beans_compile_js_fragments() should compile the jQuery fragment, saving it to the virtual filesystem and
	 * enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_jquery() {
		$id       = 'test-jquery';
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$this->add_virtual_directory( $id );
		$path = vfsStream::url( "compiled/beans/compiler/{$id}/" );

		// Run the tests.
		$this->assertEmpty( $this->get_compiled_filename( $path ) );
		beans_compile_js_fragments(
			$id,
			$fragment,
			[
				'dependencies' => [ 'jquery' ],
				'in_footer'    => true,
				'minify_js'    => true,
			]
		);
		$filename = $this->get_compiled_filename( $path );
		$this->assertFileExists( $path . $filename );
		$this->assertStringEndsWith( '.js', $filename );
		$this->assertSame( $this->get_compiled_jquery(), $this->get_cached_contents( $filename, $id ) );
	}

	/**
	 * Test beans_compile_js_fragments() should compile the JS fragment, saving it to the virtual filesystem and
	 * enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_js() {
		$id       = 'test-js';
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$this->add_virtual_directory( $id );
		$path = vfsStream::url( "compiled/beans/compiler/{$id}/" );

		// Run the tests.
		$this->assertEmpty( $this->get_compiled_filename( $path ) );
		beans_compile_js_fragments(
			$id,
			$fragment,
			[
				'in_footer' => true,
				'minify_js' => true,
			]
		);
		$filename = $this->get_compiled_filename( $path );
		$this->assertFileExists( $path . $filename );
		$this->assertStringEndsWith( '.js', $filename );
		$this->assertSame( $this->get_compiled_js(), $this->get_cached_contents( $filename, $id ) );
	}

	/**
	 * Test beans_compile_js_fragments() should compile the combined JS fragments, saving it to the virtual filesystem
	 * and enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_combined_js() {
		$id        = 'test-js';
		$fragments = [
			vfsStream::url( 'compiled/fixtures/my-game-clock.js' ),
			vfsStream::url( 'compiled/fixtures/jquery.test.js' ),
		];
		$this->add_virtual_directory( $id );
		$path = vfsStream::url( "compiled/beans/compiler/{$id}/" );

		// Run the tests.
		$this->assertEmpty( $this->get_compiled_filename( $path ) );
		beans_compile_js_fragments(
			$id,
			$fragments,
			[
				'dependencies' => [ 'jquery' ],
				'in_footer'    => true,
				'minify_js'    => true,
			]
		);
		$filename = $this->get_compiled_filename( $path );
		$this->assertFileExists( $path . $filename );
		$this->assertStringEndsWith( '.js', $filename );

		$contents = $this->get_cached_contents( $filename, $id );
		$this->assertContains( $this->get_compiled_js(), $contents );
		$this->assertContains( $this->get_compiled_jquery(), $contents );
	}
}
