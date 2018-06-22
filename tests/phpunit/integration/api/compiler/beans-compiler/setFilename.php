<?php
/**
 * Tests for the set_filename() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use _Beans_Compiler;
use org\bovigo\vfs\vfsStream;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_SetFilename
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_SetFilename extends Compiler_Test_Case {

	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		// Return the virtual filesystem's path to avoid wp_normalize_path converting its prefix from vfs::// to vfs:/.
		Monkey\Functions\when( 'wp_normalize_path' )->returnArg();
	}

	/**
	 * Test _Beans_Compiler::set_filename() should return the hash created with the modification time from each of the
	 * fragments.
	 */
	public function test_should_return_hash_created_with_fragments_filemtime() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = [
			'id'           => 'test-script',
			'type'         => 'script',
			'format'       => false,
			'fragments'    => [ $fragment ],
			'dependencies' => false,
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the mocks.
		$this->set_dev_mode( true );
		$this->add_virtual_directory( $config['id'] );

		// Set the filename. Test.
		$compiler->set_filename();
		$expected = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$this->assertSame( $expected, $compiler->filename );
	}

	/**
	 * Test _Beans_Compiler::set_filename() should exclude external fragments.
	 */
	public function test_should_exclude_external_fragments() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$config   = [
			'id'           => 'test-script',
			'type'         => 'script',
			'format'       => false,
			'fragments'    => [
				$fragment,
				'http://foo.com/my-script.js', // Should skip this one.
			],
			'dependencies' => false,
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the mocks.
		$this->set_dev_mode( true );
		$this->add_virtual_directory( $config['id'] );

		// Test that we are in dev mode & the directory does exist.
		$this->assertTrue( _beans_is_compiler_dev_mode() );
		$this->directoryExists( vfsStream::url( 'compiled/beans/compiler/test-script' ) );
		$this->assertTrue( is_dir( $compiler->dir ) );

		// Set the filename. Test.
		$compiler->set_filename();
		$expected = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$this->assertSame( $expected, $compiler->filename );
	}

	/**
	 * Test _Beans_Compiler::set_filename() should remove the old compiled file.
	 */
	public function test_should_remove_old_file() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = [
			'id'           => 'test',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => [ $fragment ],
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		];
		$compiler = new _Beans_Compiler( $config );

		// Set up the mocks.
		$this->set_dev_mode( true );
		$this->add_virtual_directory( $config['id'] );

		/**
		 * Set up the original "compiled" file. This is the file that should get removed during this
		 * test.  We add the file into the virtual filesystem.
		 */
		$original_filemtime = filemtime( $fragment );
		$original_filename  = $this->get_filename( $compiler, $config, $original_filemtime );
		vfsStream::newFile( $original_filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
			->setContent( $this->get_compiled_jquery() );
		$original_hashes = explode( '-', pathinfo( $original_filename, PATHINFO_FILENAME ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/test/' . $original_filename ) );

		/**
		 * Next step is to modify the fragment, which will change its modification time.  Let's add an opening
		 * comment to the fragment's content and then set up the modified file for our tests.
		 */
		$file = $this->mock_filesystem->getChild( 'fixtures' )->getChild( 'jquery.test.js' );
		$file->write( "// changed \n" . $file->getContent() );
		$modified_filemtime = filemtime( $fragment );
		$modified_filename  = $this->get_filename( $compiler, $config, $modified_filemtime );
		$modified_hashes    = explode( '-', pathinfo( $modified_filename, PATHINFO_FILENAME ) );

		/**
		 * We've now completed the set up process.  Let's test that the original fragment has changed by
		 * testing that it is not equal to the modification time, filename, and hashes.
		 */
		$this->assertNotEquals( $modified_filemtime, $original_filemtime );
		$this->assertNotEquals( $modified_filename, $original_filename );
		$this->assertSame( $modified_hashes[0], $original_hashes[0] );
		$this->assertNotEquals( $modified_hashes[1], $original_hashes[1] );

		/**
		 * Now let's run the compiler's set_filename() and test that:
		 *
		 * 1. The returned filename matches our modified filename.
		 * 2. The original "compiled" file was removed.
		 */
		$compiler->set_filename();
		$this->assertSame( $modified_filename, $compiler->filename );
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/test/' . $original_filename ) );
	}
}
