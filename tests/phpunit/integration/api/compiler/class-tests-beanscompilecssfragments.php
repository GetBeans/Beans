<?php
/**
 * Tests for beans_compile_css_fragments()
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
 * Class Tests_BeansCompileCssFragments
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompileCssFragments extends Compiler_Test_Case {

	/**
	 * Test beans_compile_css_fragments() should compile the CSS fragments, saving it to the virtual filesystem and
	 * enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_css() {
		$id       = 'compile-css-fragments';
		$fragment = vfsStream::url( 'compiled/fixtures/style.css' );
		$this->add_virtual_directory( $id );
		$path = vfsStream::url( "compiled/beans/compiler/{$id}/" );

		// Run the tests.
		$this->assertEmpty( $this->get_compiled_filename( $path ) );
		beans_compile_css_fragments( $id, $fragment );
		$filename = $this->get_compiled_filename( $path );
		$this->assertFileExists( $path . $filename );
		$this->assertStringEndsWith( '.css', $filename );
		$this->assertSame( $this->get_compiled_css(), $this->get_cached_contents( $filename, $id ) );
	}
}
