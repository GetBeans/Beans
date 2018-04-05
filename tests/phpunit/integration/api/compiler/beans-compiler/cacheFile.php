<?php
/**
 * Tests the cache_file method of _Beans_Compiler.
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
	 * Test cache_file() should not create the file.
	 */
	public function test_should_not_create_the_file() {
		$compiler = new _Beans_Compiler( array(
			'id'           => 'test-script',
			'type'         => 'script',
			'fragments'    => array( vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
		) );

		// Set up the tests.
		$this->set_dev_mode( false );
		$this->mock_filesystem_for_fragments( $compiler );
		$this->add_virtual_directory( $compiler->config['id'] );
		$this->set_current_fragment( $compiler, $compiler->config['fragments'][0] );
		$compiler->set_filename();
		$compiler->combine_fragments();
		$this->mock_creating_file( $compiler );

		// Run the tests.
		$this->assertFalse( $compiler->cache_file() );
		$this->assertFileNotExists( $compiler->get_filename() );
	}

	/**
	 * Test cache_file() should create the compiled jQuery file.
	 */
	public function test_should_create_compiled_jquery_file() {
		$compiler = new _Beans_Compiler( array(
			'id'           => 'test-jquery',
			'type'         => 'script',
			'fragments'    => array( vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
		) );

		// Set up the tests.
		$this->set_dev_mode( false );
		$this->mock_filesystem_for_fragments( $compiler );
		$this->add_virtual_directory( $compiler->config['id'] );
		$this->set_current_fragment( $compiler, $compiler->config['fragments'][0] );
		$compiler->set_filename();
		$compiler->combine_fragments();
		$this->mock_creating_file( $compiler, true );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_jquery(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test cache_file() should create the compiled JavaScript file.
	 */
	public function test_should_create_compiled_javascript_file() {
		$compiler = new _Beans_Compiler( array(
			'id'        => 'test-js',
			'type'      => 'script',
			'fragments' => array( vfsStream::url( 'compiled/fixtures/my-game-clock.js' ) ),
			'in_footer' => true,
			'minify_js' => true,
		) );

		// Set up the tests.
		$this->set_dev_mode( false );
		$this->mock_filesystem_for_fragments( $compiler );
		$this->add_virtual_directory( $compiler->config['id'] );
		$this->set_current_fragment( $compiler, $compiler->config['fragments'][0] );
		$compiler->set_filename();
		$compiler->combine_fragments();
		$this->mock_creating_file( $compiler, true );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_js(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test cache_file() should create the compiled CSS file.
	 */
	public function test_should_create_compiled_css_file() {
		$compiler = new _Beans_Compiler( array(
			'id'        => 'test-css',
			'type'      => 'style',
			'fragments' => array( vfsStream::url( 'compiled/fixtures/style.css' ) ),
		) );

		// Set up the tests.
		$this->set_dev_mode( false );
		$this->mock_filesystem_for_fragments( $compiler );
		$this->add_virtual_directory( $compiler->config['id'] );
		$this->set_current_fragment( $compiler, $compiler->config['fragments'][0] );
		$compiler->set_filename();
		$compiler->combine_fragments();
		$this->mock_creating_file( $compiler, true );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_css(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test cache_file() should create the compiled LESS file.
	 */
	public function test_should_create_compiled_less_file() {
		$compiler = new _Beans_Compiler( array(
			'id'        => 'test-css',
			'type'      => 'style',
			'format'    => 'less',
			'fragments' => array(
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			),
		) );

		// Set up the tests.
		$this->set_dev_mode( false );
		$this->mock_filesystem_for_fragments( $compiler );
		$this->add_virtual_directory( $compiler->config['id'] );
		$this->set_current_fragment( $compiler, $compiler->config['fragments'][0] );
		$compiler->set_filename();
		$compiler->combine_fragments();
		$this->mock_creating_file( $compiler, true );

		// Run the tests.
		$this->assertTrue( $compiler->cache_file() );
		$this->assertSame( $this->get_compiled_less(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Mock creating the file.
	 *
	 * @since 1.5.0
	 *
	 * @param _Beans_Compiler $compiler      Instance of the compiler.
	 * @param bool            $should_create Optional. When true, mock creating the file. Default is false.
	 *
	 * @return void
	 */
	private function mock_creating_file( $compiler, $should_create = false ) {
		$GLOBALS['wp_filesystem']->shouldReceive( 'put_contents' )
			->once()
			->andReturn( $should_create );

		if ( ! $should_create ) {
			return;
		}

		vfsStream::newFile( $compiler->filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $compiler->config['id'] ) )
			->setContent( $compiler->compiled_content );
	}

	/**
	 * Get the file's content.
	 *
	 * @since 1.5.0
	 *
	 * @param _Beans_Compiler $compiler Instance of the compiler.
	 *
	 * @return string
	 */
	private function get_cached_file_contents( $compiler ) {
		return $this->mock_filesystem
			->getChild( 'beans/compiler/' . $compiler->config['id'] )
			->getChild( $compiler->filename )
			->getcontent();
	}
}
