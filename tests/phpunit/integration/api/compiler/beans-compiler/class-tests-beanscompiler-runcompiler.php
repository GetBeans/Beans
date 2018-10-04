<?php
/**
 * Tests for the run_compiler() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_RunCompiler
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_RunCompiler extends Compiler_Test_Case {

	/**
	 * The CSS content.
	 *
	 * @var string
	 */
	protected $css;

	/**
	 * The Less content.
	 *
	 * @var string
	 */
	protected $less;

	/**
	 * The jQuery content.
	 *
	 * @var string
	 */
	protected $jquery;

	/**
	 * The JavaScript content.
	 *
	 * @var string
	 */
	protected $js;

	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$fixtures     = $this->mock_filesystem->getChild( 'fixtures' );
		$this->css    = $fixtures->getChild( 'style.css' )->getContent();
		$this->less   = $fixtures->getChild( 'variables.less' )->getContent() . $fixtures->getChild( 'test.less' )->getContent();
		$this->jquery = $fixtures->getChild( 'jquery.test.js' )->getContent();
	}

	/**
	 * Test _Beans_Compiler::cache_file() should enqueue the existing cached file when no modifications (no fragments
	 * have changed to warrant re-compiling the file).
	 */
	public function test_should_enqueue_existing_cached_file_when_no_modifications() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = [
			'id'           => 'test-jquery',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => [ vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ],
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => '1.5.0',
		];
		$compiler = new _Beans_Compiler( $config );

		// Store the cached file into the virtual filesystem.
		$this->add_virtual_directory( $config['id'] );
		$original_filename = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$original_file     = vfsStream::url( 'compiled/beans/compiler/' . $config['id'] . '/' . $original_filename );
		vfsStream::newFile( $original_filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
			->setContent( $this->get_compiled_jquery() );
		$this->assertFileExists( $original_file );

		// Run the compiler.
		$compiler->run_compiler();
		$actual_filename = $compiler->get_filename();

		// Check that the "compiled" filename did not change.
		$this->assertSame( $original_file, $actual_filename );

		// Check that the file still exists.
		$this->assertFileExists( $actual_filename );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should recompile when a fragment(s) changes.  When this happens, the existing
	 * cached file is removed and the new file is stored in the filesystem.
	 */
	public function test_should_recompile_when_fragments_change() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = [
			'id'           => 'test-script',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => [ $fragment ],
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		];
		$compiler = new _Beans_Compiler( $config );

		/**
		 * Set up the original "compiled" file. This is the file that should get removed during this
		 * test.  We add the file into the virtual filesystem.
		 */
		$this->add_virtual_directory( $config['id'] );
		$original_filemtime = filemtime( $fragment );
		$original_filename  = $this->get_filename( $compiler, $config, $original_filemtime );
		$original_file      = vfsStream::url( 'compiled/beans/compiler/' . $config['id'] . '/' . $original_filename );
		vfsStream::newFile( $original_filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
			->setContent( $this->get_compiled_jquery() );
		$original_hashes = explode( '-', pathinfo( $original_filename, PATHINFO_FILENAME ) );
		$this->assertFileExists( $original_file );

		/**
		 * Next step is to modify the fragment, which will change its modification time.  Let's add an opening
		 * comment to the fragment's content and then set up the modified file for our tests.
		 */
		$this->mock_filesystem->getChild( 'fixtures' )
			->getChild( 'jquery.test.js' )
			->write( $this->jquery . "\n console.log( 'Beans rocks!' ); \n" );
		$modified_filemtime = filemtime( $fragment );
		$modified_filename  = $this->get_filename( $compiler, $config, $modified_filemtime );
		$modified_hashes    = explode( '-', pathinfo( $modified_filename, PATHINFO_FILENAME ) );
		$modified_file      = vfsStream::url( 'compiled/beans/compiler/' . $config['id'] . '/' . $modified_filename );
		$this->assertFileNotExists( $modified_file );

		/**
		 * We've now completed the setup process.  Let's test that the original fragment has changed by
		 * testing that it is not equal to the modification time, filename, and hashes.
		 */
		$this->assertNotEquals( $modified_filemtime, $original_filemtime );
		$this->assertNotEquals( $modified_filename, $original_filename );
		$this->assertSame( $modified_hashes[0], $original_hashes[0] );
		$this->assertNotEquals( $modified_hashes[1], $original_hashes[1] );

		// Run the compiler.
		$compiler->run_compiler();
		$actual_filename = $compiler->get_filename();

		// Check that the "compiled" filename is different.
		$this->assertNotEquals( $original_file, $actual_filename );
		$this->assertSame( $modified_file, $actual_filename );

		// Check that the original cached file was removed.
		$this->assertFileNotExists( $original_file );

		// Check that a new file was cached.
		$this->assertFileExists( $modified_file );
		$this->assertFileExists( $actual_filename );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should compile jQuery, saving it to the virtual filesystem and enqueuing it
	 * in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_jquery() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = [
			'id'           => 'test-jquery',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => [ $fragment ],
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the virtual directory.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config, filemtime( $fragment ) )
		);

		// Run the compiler. Test.
		$compiler->run_compiler();
		$actual_filename = $compiler->get_filename();
		$this->assertFileExists( $actual_filename );
		$this->assertSame( $expected_file, $actual_filename );
		$this->assertSame( $this->get_compiled_jquery(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should compile JavaScript, saving it to the virtual filesystem and enqueuing
	 * it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_js() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$config   = [
			'id'           => 'test-js',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => [ $fragment ],
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the virtual directory.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config, filemtime( $fragment ) )
		);

		// Run the compiler. Test.
		$compiler->run_compiler();
		$actual_filename = $compiler->get_filename();
		$this->assertFileExists( $actual_filename );
		$this->assertSame( $expected_file, $actual_filename );
		$this->assertSame( $this->get_compiled_js(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should compile CSS, saving it to the virtual filesystem and enqueuing it in
	 * WordPress.
	 */
	public function test_should_compile_save_and_enqueue_css() {
		$fragment = vfsStream::url( 'compiled/fixtures/style.css' );
		$config   = [
			'id'           => 'test-css',
			'type'         => 'style',
			'format'       => 'css',
			'fragments'    => [ $fragment ],
			'dependencies' => false,
			'in_footer'    => false,
			'minify_js'    => false,
			'version'      => '1.5.0',
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the virtual directory.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config, filemtime( $fragment ) )
		);

		// Run the compiler. Test.
		$compiler->run_compiler();
		$actual_filename = $compiler->get_filename();
		$this->assertFileExists( $actual_filename );
		$this->assertSame( $expected_file, $actual_filename );
		$this->assertSame( $this->get_compiled_css(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );
	}

	/**
	 * Test _Beans_Compiler::cache_file() should compile Less, saving it to the virtual filesystem and enqueuing it in
	 * WordPress.
	 */
	public function test_should_compile_save_and_enqueue_less() {
		$config   = [
			'id'           => 'test-css',
			'type'         => 'style',
			'format'       => 'less',
			'fragments'    => [
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			],
			'dependencies' => false,
			'in_footer'    => false,
			'minify_js'    => false,
			'version'      => '1.5.0',
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the virtual directory.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config )
		);

		// Run the compiler. Test.
		$compiler->run_compiler();
		$actual_filename = $compiler->get_filename();
		$this->assertFileExists( $actual_filename );
		$this->assertSame( $expected_file, $actual_filename );
		$this->assertSame( $this->get_compiled_less(), $this->get_cached_contents( $compiler->filename, $config['id'] ) );
	}

	/**
	 * Get the filename from the compiler.
	 *
	 * @since 1.5.0
	 *
	 * @param _Beans_Compiler $compiler Instance of the compiler.
	 *
	 * @return string
	 */
	protected function get_actual_filename( $compiler ) {
		$path = $compiler->get_filename();

		if ( substr( $path, 0, 6 ) === 'vfs://' ) {
			return $path;
		}

		return str_replace( 'vfs:/', 'vfs://', $path );
	}
}
