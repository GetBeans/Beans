<?php
/**
 * Tests for the run_compiler() method of Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_RunCompiler
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
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
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$fixtures     = $this->mock_filesystem->getChild( 'fixtures' );
		$this->css    = $fixtures->getChild( 'style.css' )->getContent();
		$this->less   = $fixtures->getChild( 'variables.less' )->getContent() . $fixtures->getChild( 'test.less' )->getContent();
		$this->jquery = $fixtures->getChild( 'jquery.test.js' )->getContent();
		$this->js     = $fixtures->getChild( 'my-game-clock.js' )->getContent();
	}

	/**
	 * Test Beans_Compiler::run_compiler() should enqueue the existing cached file when no modifications (no fragments
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
		$compiler = $this->create_compiler( $config );

		// Store the cached file into the virtual filesystem.
		$filename    = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$cached_file = vfsStream::url( 'compiled/beans/compiler/' . $config['id'] . '/' . $filename );
		$this->create_virtual_file( $config['id'], $filename, $this->get_compiled_jquery() );

		// Prepare the mocks.
		global $_beans_compiler_added_fragments;
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( $config['id'], $_beans_compiler_added_fragments[ $config['format'] ] )
			->andReturn( $_beans_compiler_added_fragments[ $config['format'] ] );
		Monkey\Functions\expect( 'is_ssl' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_enqueue_script' )
			->once()
			->with(
				$config['id'],
				str_replace( 'vfs://compiled/', $this->compiled_url, $cached_file ),
				$config['dependencies'],
				$config['version'],
				$config['in_footer']
			)
			->andReturnNull();
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );

		// Check that the file is cached before we start the compiler.
		$this->assertFileExists( $cached_file );

		// Run the compiler.
		$compiler->run_compiler();

		// Check that the "compiled" filename did not change.
		$this->assertSame( $cached_file, $compiler->get_filename() );

		// Check that the file still exists.
		$this->assertFileExists( $compiler->get_filename() );
	}

	/**
	 * Test Beans_Compiler::run_compiler() should recompile when a fragment(s) changes.  When this happens, the existing
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
		$compiler = $this->create_compiler( $config );

		/**
		 * Step 1: Store the "original" cached file into the virtual filesystem.
		 * This is the file that should get removed during this test.
		 */
		$original_filemtime = filemtime( $fragment );
		$original_filename  = $this->get_filename( $compiler, $config, $original_filemtime );
		$this->create_virtual_file( $config['id'], $original_filename, $this->get_compiled_jquery() );

		$original_cached_file = vfsStream::url( 'compiled/beans/compiler/test-script/' . $original_filename );
		$original_hashes      = explode( '-', pathinfo( $original_filename, PATHINFO_FILENAME ) );

		/**
		 * Step 2: Modify the fragment.
		 * Modifying the fragment will change its modification time, which should trigger recompiling and storing
		 * a new cached file.
		 */
		$this->mock_filesystem->getChild( 'fixtures' )
			->getChild( 'jquery.test.js' )
			->write( $this->jquery . "\n console.log( 'Beans rocks!' ); \n" );
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );

		$modified_filemtime = filemtime( $fragment );
		$modified_filename  = $this->get_filename( $compiler, $config, $modified_filemtime );
		$modified_file      = vfsStream::url( 'compiled/beans/compiler/test-script/' . $modified_filename );
		$modified_hashes    = explode( '-', pathinfo( $modified_filename, PATHINFO_FILENAME ) );

		/**
		 * Step 3: Test the starting conditions.
		 *
		 * 1. Check that the original file is cached.
		 * 2. Check that the new "modified" file is not yet cached (as that happens when we run the compiler).
		 * 3. Compare the "original" file to the new "modified" file to ensure they are different.
		 */
		$this->assertFileExists( $original_cached_file );
		$this->assertFileNotExists( $modified_file );
		$this->assertNotEquals( $original_filemtime, $modified_filemtime );
		$this->assertNotEquals( $modified_filename, $original_filename );
		$this->assertSame( $modified_hashes[0], $original_hashes[0] );
		$this->assertNotEquals( $modified_hashes[1], $original_hashes[1] );

		// Step 4: Prepare the mocks.
		$this->prepare_mocks( $compiler, $config, $modified_file, $this->get_compiled_jquery() . "console.log('Beans rocks!');" );

		// Step 5: Run the compiler and then the tests.
		$compiler->run_compiler();

		// Check that the "compiled" filename is different.
		$this->assertNotEquals( $compiler->get_filename(), $original_cached_file );
		$this->assertSame( $modified_file, $compiler->get_filename() );

		// Check that the original cached file was removed.
		$this->assertFileNotExists( $original_cached_file );

		// Check that a new file was cached.
		$this->assertFileExists( $modified_file );
		$this->assertFileExists( $compiler->get_filename() );
	}

	/**
	 * Test Beans_Compiler::run_compiler() should compile jQuery, saving it to the virtual filesystem and enqueuing it
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
		$compiler = $this->create_compiler( $config );

		// Prepare the mocks.
		$expected_cache_filename = $this->get_cache_filename( $compiler, $config, $fragment );
		$this->prepare_mocks( $compiler, $config, $expected_cache_filename, $this->get_compiled_jquery() );

		// Run the tests.
		$this->assertFileNotExists( $expected_cache_filename );
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_cache_filename, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_jquery(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test Beans_Compiler::run_compiler() should compile JavaScript, saving it to the virtual filesystem and enqueuing
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
		$compiler = $this->create_compiler( $config );

		// Prepare the mocks.
		$expected_cache_filename = $this->get_cache_filename( $compiler, $config, $fragment );
		$this->prepare_mocks( $compiler, $config, $expected_cache_filename, $this->get_compiled_js() );

		// Run the tests.
		$this->assertFileNotExists( $expected_cache_filename );
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_cache_filename, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_js(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test Beans_Compiler::run_compiler() should compile CSS, saving it to the virtual filesystem and enqueuing it in
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
		$compiler = $this->create_compiler( $config );

		// Prepare the mocks.
		$expected_cache_filename = $this->get_cache_filename( $compiler, $config, $fragment );
		$this->prepare_mocks( $compiler, $config, $expected_cache_filename, $this->get_compiled_css() );

		// Run the tests.
		$this->assertFileNotExists( $expected_cache_filename );
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_cache_filename, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_css(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Test Beans_Compiler::run_compiler() should compile Less, saving it to the virtual filesystem and enqueuing it in
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
		$compiler = $this->create_compiler( $config );

		// Prepare the mocks.
		$expected_cache_filename = $this->get_cache_filename( $compiler, $config );
		$this->prepare_mocks( $compiler, $config, $expected_cache_filename, $this->get_compiled_less() );

		// Run the tests.
		$this->assertFileNotExists( $expected_cache_filename );
		$compiler->run_compiler();
		$this->assertFileExists( $compiler->get_filename() );
		$this->assertSame( $expected_cache_filename, $compiler->get_filename() );
		$this->assertSame( $this->get_compiled_less(), $this->get_cached_file_contents( $compiler ) );
	}

	/**
	 * Builds and returns the filename for the expected "cache" file.
	 *
	 * @since 1.0.0
	 *
	 * @param \Beans_Compiler $compiler Instance of the compiler.
	 * @param array            $config   Compiler's configuration parameters.
	 * @param string           $fragment The fragment.
	 *
	 * @return string
	 */
	protected function get_cache_filename( \Beans_Compiler $compiler, array $config, $fragment = '' ) {
		$this->add_virtual_directory( $config['id'] );

		return vfsStream::url(
			sprintf(
				'compiled/beans/compiler/%s/%s',
				$config['id'],
				$this->get_filename( $compiler, $config, $fragment ? filemtime( $fragment ) : null )
			)
		);
	}

	/**
	 * Prepare the mocks for this test.
	 *
	 * @since 1.5.0
	 *
	 * @param Beans_Compiler $compiler    Instance of the compiler.
	 * @param array           $config      The compiler's configuration.
	 * @param string          $file        The absolute path to the compiled file.
	 * @param string          $content     Optional. The expected compiled content.
	 * @param bool            $in_dev_mode Optional. When true, turns on dev mode. Default is false.
	 *
	 * @return void
	 */
	private function prepare_mocks( $compiler, $config, $file, $content = '', $in_dev_mode = false ) {
		global $_beans_compiler_added_fragments;
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( $config['id'], $_beans_compiler_added_fragments[ $config['format'] ] )
			->andReturn( $_beans_compiler_added_fragments[ $config['format'] ] );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( $in_dev_mode );
		Monkey\Functions\when( 'is_ssl' )->justReturn( false );
		Monkey\Functions\expect( 'beans_url_to_path' )->never();
		Monkey\Functions\expect( 'wp_remote_get' )->never();

		if ( 'script' === $config['type'] ) {
			Monkey\Functions\expect( 'wp_enqueue_script' )
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
			Monkey\Functions\expect( 'wp_enqueue_style' )
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
			->andReturnUsing(
				function( $file, $content ) use ( $compiler, $config ) {
						$pathinfo = pathinfo( $file );

						// Add the new file into the virtual filesystem.
						vfsStream::newFile( $pathinfo['basename'] )
						->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
						->setContent( $content );

						return true;
				}
			);
	}

	/**
	 * Get the file's content.
	 *
	 * @since 1.5.0
	 *
	 * @param Beans_Compiler $compiler Instance of the compiler.
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
