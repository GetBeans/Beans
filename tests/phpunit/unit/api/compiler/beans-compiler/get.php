<?php
/**
 * Tests the __get() method of _Beans_Compiler.
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
 * Class Tests_BeansCompiler_Get
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_Get extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::_get() should fix the configuration's dependency key.
	 */
	public function test_should_fix_configuration_dependency_key() {
		$config = [
			'id'          => 'test',
			'type'        => 'script',
			'depedencies' => [ 'jquery' ],
		];

		$compiler = $this->create_compiler( $config );

		// Run the tests.
		$this->assertArrayNotHasKey( 'depedencies', $compiler->config );
		$this->assertArrayHasKey( 'dependencies', $compiler->config );
		$this->assertSame( [ 'jquery' ], $compiler->config['dependencies'] );
	}

	/**
	 * Test _Beans_Compiler::_get() should return the configuration.
	 */
	public function test_should_return_configuration() {
		$compiler = $this->create_compiler( [
			'id'     => 'test',
			'type'   => 'style',
			'format' => 'less',
		] );
		$this->assertSame(
			[
				'id'           => 'test',
				'type'         => 'style',
				'format'       => 'less',
				'fragments'    => [],
				'dependencies' => false,
				'in_footer'    => false,
				'minify_js'    => false,
				'version'      => false,
			],
			$compiler->config
		);

		$compiler = $this->create_compiler( [
			'id'           => 'test_scripts',
			'type'         => 'script',
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'version'      => null,
		] );
		$this->assertSame(
			[
				'id'           => 'test_scripts',
				'type'         => 'script',
				'format'       => false,
				'fragments'    => [],
				'dependencies' => [ 'jquery' ],
				'in_footer'    => true,
				'minify_js'    => false,
				'version'      => null,
			],
			$compiler->config
		);
	}

	/**
	 * Test _Beans_Compiler::_get() should return the absolute path to the compiled files directory.
	 */
	public function test_should_return_absolute_path_to_compiled_files_dir() {
		$config = [
			'id'     => 'test',
			'type'   => 'style',
			'format' => 'less',
		];

		$compiler = $this->create_compiler( $config );

		// Run the test.
		$this->assertSame( vfsStream::url( 'compiled/beans/compiler/test' ), $compiler->dir );
	}

	/**
	 * Test _Beans_Compiler::_get() should return URL to the compiled files directory.
	 */
	public function test_should_return_url_to_compiled_files_dir() {
		$config = [
			'id'   => 'test_scripts',
			'type' => 'script',
		];

		$compiler = $this->create_compiler( $config );

		// Run the test.
		$this->assertSame( $this->compiled_url . 'beans/compiler/test_scripts', $compiler->url );
	}
}
