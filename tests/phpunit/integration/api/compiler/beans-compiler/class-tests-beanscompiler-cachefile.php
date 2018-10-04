<?php
/**
 * Tests for the cache_file() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use Mockery;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_CacheFile
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_CacheFile extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::cache_file() should create the compiled jQuery file.
	 */
	public function test_should_create_compiled_jquery_file() {
		$config   = [
			'id'           => 'test-jquery',
			'type'         => 'script',
			'fragments'    => [ vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ],
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
		];
		$compiler = $this->initialize_compiler( $config );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_jquery(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );

		// Clean Up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should create the compiled JavaScript file.
	 */
	public function test_should_create_compiled_javascript_file() {
		$config   = [
			'id'        => 'test-js',
			'type'      => 'script',
			'fragments' => [ vfsStream::url( 'compiled/fixtures/my-game-clock.js' ) ],
			'in_footer' => true,
			'minify_js' => true,
		];
		$compiler = $this->initialize_compiler( $config );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_js(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );

		// Clean Up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should create the compiled CSS file.
	 */
	public function test_should_create_compiled_css_file() {
		$config   = [
			'id'        => 'test-css',
			'type'      => 'style',
			'fragments' => [ vfsStream::url( 'compiled/fixtures/style.css' ) ],
		];
		$compiler = $this->initialize_compiler( $config );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_css(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );

		// Clean Up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should create the compiled LESS file.
	 */
	public function test_should_create_compiled_less_file() {
		$config   = [
			'id'        => 'test-css',
			'type'      => 'style',
			'format'    => 'less',
			'fragments' => [
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			],
		];
		$compiler = $this->initialize_compiler( $config );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_less(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );

		// Clean Up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Initialize the Compiler for the test.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Array of runtime configuration parameters.
	 *
	 * @return _Beans_Compiler
	 * @throws \ReflectionException Throws an error.
	 */
	protected function initialize_compiler( array $config ) {
		$compiler = new _Beans_Compiler( $config );

		add_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
		$compiler->filesystem();
		$this->add_virtual_directory( $config['id'] );
		$this->set_current_fragment( $compiler, $config['fragments'][0] );
		$compiler->set_filename();
		$compiler->combine_fragments();

		return $compiler;
	}
}
