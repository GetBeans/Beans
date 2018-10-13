<?php
/**
 * Tests for beans_flush_compiler()
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Options_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansFlushCompiler
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansFlushCompiler extends Compiler_Options_Test_Case {

	/**
	 * Test beans_flush_compiler() should bail out when the directory does not exist.
	 */
	public function test_should_bail_out_when_directory_does_not_exist() {
		$compiler_dir = vfsStream::url( 'compiled/beans/compiler/' );
		Monkey\Functions\expect( 'beans_get_compiler_dir' )
			->twice()
			->with( false )
			->andReturn( $compiler_dir );
		Monkey\Functions\expect( 'beans_remove_dir' )->never();

		$this->assertNull( beans_flush_compiler( 'beans' ) );
	}

	/**
	 * Test beans_flush_compiler() should remove cached CSS file.
	 */
	public function test_should_remove_cached_css_file() {
		$this->overwrite_virtual_filesystem();

		$compiler_dir = vfsStream::url( 'compiled/beans/compiler/' );
		Monkey\Functions\expect( 'beans_get_compiler_dir' )
			->once()
			->with( false )
			->andReturn( $compiler_dir );
		Monkey\Functions\expect( 'beans_remove_dir' )->never();

		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
		$this->assertNull( beans_flush_compiler( 'beans', 'css' ) );
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
	}

	/**
	 * Test beans_flush_compiler() should remove cached JS file.
	 */
	public function test_should_remove_cached_js_file() {
		$this->overwrite_virtual_filesystem();

		$compiler_dir = vfsStream::url( 'compiled/beans/compiler/' );
		Monkey\Functions\expect( 'beans_get_compiler_dir' )
			->once()
			->with( false )
			->andReturn( $compiler_dir );
		Monkey\Functions\expect( 'beans_remove_dir' )->never();

		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
		$this->assertNull( beans_flush_compiler( 'beans', 'js' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
	}

	/**
	 * Test beans_flush_compiler() should remove all cached files.
	 */
	public function test_should_remove_all_cached_files() {
		$this->overwrite_virtual_filesystem();

		$compiler_dir = vfsStream::url( 'compiled/beans/compiler/' );
		Monkey\Functions\expect( 'beans_get_compiler_dir' )
			->once()
			->with( false )
			->andReturn( $compiler_dir );
		Monkey\Functions\expect( 'beans_remove_dir' )
			->once()
			->with( trailingslashit( $compiler_dir ) . 'beans' )
			->andReturnUsing(
				function( $dir_path ) {
					$items = scandir( $dir_path );
					unset( $items[0], $items[1] );

					$dir_path = trailingslashit( $dir_path );

					foreach ( $items as $needle => $item ) {
						unlink( $dir_path . $item );
					}

					return rmdir( $dir_path );
				}
			);

		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/1234567-9876543.css' ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/beans/abcd3fg-hijklmn.js' ) );
		$this->assertNull( beans_flush_compiler( 'beans' ) );
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

	/**
	 * Get the compiled JavaScript.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function get_compiled_js() {
		return <<<EOB
class MyGameClock{constructor(maxTime){this.maxTime=maxTime;this.currentClock=0;}
getRemainingTime(){return this.maxTime-this.currentClock;}}
EOB;
	}

	/**
	 * Get the compiled CSS.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function get_compiled_css() {
		return <<<EOB
body{background-color:#fff;color:#000;font-size:18px}
a{color:#cc0000}
p{margin-bottom:30px}
EOB;
	}
}
