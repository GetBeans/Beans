<?php
/**
 * Tests for beans_compile_less_fragments()
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
 * Class Tests_BeansCompileLessFragments
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompileLessFragments extends Compiler_Test_Case {

	/**
	 * Test beans_compile_less_fragments() should compile the Less fragments, saving it to the virtual filesystem and
	 * enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_less() {
		$id        = 'compile-less-fragments';
		$fragments = [
			vfsStream::url( 'compiled/fixtures/test.less' ),
			vfsStream::url( 'compiled/fixtures/variables.less' ),
		];
		$this->add_virtual_directory( $id );
		$path = vfsStream::url( "compiled/beans/compiler/{$id}/" );

		// Run the tests.
		$this->assertEmpty( $this->get_compiled_filename( $path ) );
		beans_compile_less_fragments( $id, $fragments );
		$filename = $this->get_compiled_filename( $path );
		$this->assertFileExists( $path . $filename );
		$this->assertStringEndsWith( '.css', $filename );
		$this->assertSame( $this->get_compiled_less(), $this->get_cached_contents( $filename, $id ) );
	}
}
