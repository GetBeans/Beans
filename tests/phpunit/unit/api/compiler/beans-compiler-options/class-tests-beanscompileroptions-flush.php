<?php
/**
 * Tests for the flush() method of Beans_Compiler_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans_Compiler_Options;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Options_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansCompilerOptions_Flush
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_Flush extends Compiler_Options_Test_Case {

	/**
	 * Test Beans_Compiler_Options::flush() should not remove the cached directory when this is not a 'compiler
	 * cache flush'.
	 */
	public function test_should_not_remove_cached_dir_when_not_a_flush() {
		// Check that the cached directory exists before we start.
		$this->directoryExists( vfsStream::url( 'compiled/beans/compiler/' ) );

		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_flush_compiler_cache' )
			->andReturnNull();
		Monkey\Functions\expect( 'beans_get_compiler_dir' )->never();
		Monkey\Functions\expect( 'beans_remove_dir' )->never();

		$this->assertNull( ( new Beans_Compiler_Options() )->flush() );

		// Check that it still exists and was not removed.
		$this->directoryExists( vfsStream::url( 'compiled/beans/compiler/' ) );
	}

	/**
	 * Test Beans_Compiler_Options::flush() should remove the cached directory.
	 */
	public function test_should_remove_cached_dir() {
		// Check that the cached directory exists before we start.
		$this->directoryExists( vfsStream::url( 'compiled/beans/compiler/' ) );

		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_flush_compiler_cache' )
			->andReturnFirstArg();
		Monkey\Functions\expect( 'beans_get_compiler_dir' )
			->once()
			->andReturn( vfsStream::url( 'compiled/beans/compiler/' ) );
		Monkey\Functions\expect( 'beans_remove_dir' )
			->once()
			->with( vfsStream::url( 'compiled/beans/compiler/' ) )
			->andReturnUsing(
				function() {
						// Keep it simple. Remove by redefining.
						vfsStream::setup( 'compiled', 0755, [ 'beans' => [] ] );
				}
			);

		$this->assertNull( ( new Beans_Compiler_Options() )->flush() );
		$this->assertDirectoryNotExists( vfsStream::url( 'compiled/beans/compiler/' ) );
	}
}
