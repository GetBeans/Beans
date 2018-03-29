<?php
/**
 * Tests the run_compiler method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey\Functions;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_Run_Compiler
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_Beans_Compiler_Run_Compiler extends Compiler_Test_Case {

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
	protected function setUp() {
		parent::setUp();

		// Set up the global fragments container.
		global $_beans_compiler_added_fragments;
		$_beans_compiler_added_fragments = array(
			'css'  => array(),
			'less' => array(),
			'js'   => array(),
		);

		$fixtures     = $this->mock_filesystem->getChild( 'fixtures' );
		$this->css    = $fixtures->getChild( 'style.css' )->getContent();
		$this->less   = $fixtures->getChild( 'variables.less' )->getContent() . $fixtures->getChild( 'test.less' )
				->getContent();
		$this->jquery = $fixtures->getChild( 'jquery.test.js' )->getContent();
		$this->js     = $fixtures->getChild( 'my-game-clock.js' )->getContent();
	}

	/**
	 * Test cache_file() should enqueue the existing cached file when no modifications (no fragments
	 * have changed to warrant re-compiling the file).
	 */
	public function test_should_enqueue_existing_cached_file_when_no_modifications() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = array(
			'id'           => 'test-jquery',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => array( vfsStream::url( 'compiled/fixtures/jquery.test.js' ) ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => '1.5.0',
		);
		$compiler = $this->create_compiler( $config );

		// Store the cached file into the virtual filesystem.
		$this->add_virtual_directory( $config['id'] );
		$original_filename = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$original_file     = vfsStream::url( 'compiled/beans/compiler/' . $config['id'] . '/' . $original_filename );
		vfsStream::newFile( $original_filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
			->setContent( $this->get_compiled_jquery() );
		$this->assertFileExists( $original_file );

		// Set up the mocks.
		$this->set_up_mocks( $compiler, $config, $original_file );

		// Run the compiler.
		$compiler->run_compiler();

		// Check that the "compiled" filename did not change.
		$this->assertSame( $original_file, $compiler->get_filename() );

		// Check that the file still exists.
		$this->assertFileExists( $compiler->get_filename() );
	}

	/**
	 * Test cache_file() should recompile when a fragment(s) changes.  When this happens, the existing cached file
	 * is removed and the new file is stored in the filesystem.
	 */
	public function test_should_recompile_when_fragments_change() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = array(
			'id'           => 'test-script',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => array( $fragment ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		);
		$compiler = $this->create_compiler( $config );

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
		$compiled_content = $this->get_compiled_jquery() . "console.log('Beans rocks!');";
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

		// Set up the mocks.
		$this->set_up_mocks( $compiler, $config, $modified_file, $compiled_content );

		// Run the compiler.
		$compiler->run_compiler();

		// Check that the "compiled" filename is different.
		$this->assertNotEquals( $compiler->get_filename(), $original_file );
		$this->assertSame( $modified_file, $compiler->get_filename() );

		// Check that the original cached file was removed.
		$this->assertFileNotExists( $original_file );

		// Check that a new file was cached.
		$this->assertFileExists( $modified_file );
		$this->assertFileExists( $compiler->get_filename() );
	}

	/**
	 * Test cache_file() should compile jQuery, saving it to the virtual filesystem and enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_jquery() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = array(
			'id'           => 'test-jquery',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => array( $fragment ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		);
		$compiler = $this->create_compiler( $config );

		// Set up the mocks.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config, filemtime( $fragment ) )
		);

		$this->set_up_mocks( $compiler, $config, $expected_file, $this->get_compiled_jquery() );

		// Run the compiler. Test.
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_file, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_jquery(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test cache_file() should compile JavaScript, saving it to the virtual filesystem and enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_js() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$config   = array(
			'id'           => 'test-js',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => array( $fragment ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		);
		$compiler = $this->create_compiler( $config );

		// Set up the mocks.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config, filemtime( $fragment ) )
		);
		$this->set_up_mocks( $compiler, $config, $expected_file, $this->get_compiled_js() );

		// Run the compiler. Test.
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_file, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_js(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test cache_file() should compile CSS, saving it to the virtual filesystem and enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_css() {
		$fragment = vfsStream::url( 'compiled/fixtures/style.css' );
		$config   = array(
			'id'           => 'test-css',
			'type'         => 'style',
			'format'       => 'css',
			'fragments'    => array( $fragment ),
			'dependencies' => false,
			'in_footer'    => false,
			'minify_js'    => false,
			'version'      => '1.5.0',
		);
		$compiler = $this->create_compiler( $config );

		// Set up the mocks.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config, filemtime( $fragment ) )
		);
		$this->set_up_mocks( $compiler, $config, $expected_file, $this->get_compiled_css() );

		// Run the compiler. Test.
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_file, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_css(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test cache_file() should compile Less, saving it to the virtual filesystem and enqueuing it in WordPress.
	 */
	public function test_should_compile_save_and_enqueue_less() {
		$config   = array(
			'id'           => 'test-css',
			'type'         => 'style',
			'format'       => 'less',
			'fragments'    => array(
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			),
			'dependencies' => false,
			'in_footer'    => false,
			'minify_js'    => false,
			'version'      => '1.5.0',
		);
		$compiler = $this->create_compiler( $config );

		// Set up the mocks.
		$this->add_virtual_directory( $config['id'] );
		$expected_file = vfsStream::url(
			'compiled/beans/compiler/' . $config['id'] . '/' . $this->get_filename( $compiler, $config )
		);
		$this->set_up_mocks( $compiler, $config, $expected_file, $this->get_compiled_less() );

		// Run the compiler. Test.
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_file, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_less(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Set up the mocks for this test.
	 *
	 * @since 1.5.0
	 *
	 * @param _Beans_Compiler $compiler    Instance of the compiler.
	 * @param array           $config      The compiler's configuration.
	 * @param string          $file        The absolute path to the compiled file.
	 * @param string          $content     Optional. The expected compiled content.
	 * @param bool            $in_dev_mode Optional. When true, turns on dev mode. Default is false.
	 *
	 * @return void
	 */
	private function set_up_mocks( $compiler, $config, $file, $content = '', $in_dev_mode = false ) {
		$this->mock_dev_mode( $in_dev_mode );
		Functions\when( 'is_ssl' )->justReturn( false );

		if ( 'script' === $config['type'] ) {
			Functions\expect( 'wp_enqueue_script' )
				->once()
				->with(
					$config['id'],
					str_replace( 'vfs://compiled/', $this->compiled_url, $file ),
					$config['dependencies'],
					$config['version'],
					$config['in_footer']
				)
				->andReturnNull();
		}

		if ( 'style' === $config['type'] ) {
			Functions\expect( 'wp_enqueue_style' )
				->once()
				->with(
					$config['id'],
					str_replace( 'vfs://compiled/', $this->compiled_url, $file ),
					$config['dependencies'],
					$config['version']
				)
				->andReturnNull();
		}

		if ( empty( $content ) ) {
			return;
		}

		$this->mock_filesystem_for_fragments( $compiler );

		$GLOBALS['wp_filesystem']->shouldReceive( 'put_contents' )
			->once()
			->with( $file, $content, FS_CHMOD_FILE )
			->andReturnUsing( function( $file, $content ) use ( $compiler, $config ) {
				$pathinfo = pathinfo( $file );

				// Add the new file into the virtual filesystem.
				vfsStream::newFile( $pathinfo['basename'] )
					->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
					->setContent( $content );

				return true;
			} );
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
			->getContent();
	}
}
