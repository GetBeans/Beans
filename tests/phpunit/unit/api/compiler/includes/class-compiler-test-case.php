<?php
/**
 * Tests Case for Beans' Compiler API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 */
abstract class Compiler_Test_Case extends Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Path to the compiled files' directory.
	 *
	 * @var string
	 */
	protected $compiled_dir;

	/**
	 * Path to the compiled files directory's URL.
	 *
	 * @var string
	 */
	protected $compiled_url;

	/**
	 * Instance of vfsStreamDirectory to mock the filesystem.
	 *
	 * @var vfsStreamDirectory
	 */
	protected $mock_filesystem;

	/**
	 * Flag is in admin area (back-end).
	 *
	 * @var bool
	 */
	protected $is_admin = false;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		require_once BEANS_TESTS_LIB_DIR . 'api/compiler/class-beans-compiler.php';
		require_once BEANS_TESTS_LIB_DIR . 'api/compiler/functions.php';

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', 0644 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Valid constant.
		}
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();
		$this->compiled_dir = vfsStream::url( 'compiled' );
		$this->compiled_url = 'http:://beans.local/compiled/';

		Functions\when( 'wp_upload_dir' )->justReturn( array(
			'path'    => '',
			'url'     => '',
			'subdir'  => '',
			'basedir' => $this->compiled_dir,
			'baseurl' => $this->compiled_url,
			'error'   => false,
		) );
		Functions\when( 'is_admin' )->justReturn( $this->is_admin );
		Functions\when( 'site_url' )->justReturn( 'http:://beans.local' );

		$this->load_original_functions( array(
			'api/utilities/functions.php',
		) );
	}

	/**
	 * Tear down the test fixture.
	 */
	protected function tearDown() {
		Mockery::close();
		parent::tearDown();

		// Reset the global fragments container.
		global $_beans_compiler_added_fragments;
		$_beans_compiler_added_fragments = array(
			'css'  => array(),
			'less' => array(),
			'js'   => array(),
		);

		unset( $GLOBALS['wp_filesystem'] );
	}

	/**
	 * Set up the virtual filesystem.
	 */
	private function set_up_virtual_filesystem() {
		$structure = array(
			'beans'    => array(
				'compiler'       => array(
					'index.php' => '',
				),
				'admin-compiler' => array(
					'index.php' => '',
				),
			),
			'fixtures' => array(),
		);

		$filenames    = array( 'jquery.test.js', 'my-game-clock.js', 'style.css', 'test.less', 'variables.less' );
		$fixtures_dir = basename( __DIR__ ) === 'compiler'
			? __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR
			: dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR;

		// Load the fixture files and content into the virtual filesystem.
		foreach ( $filenames as $filename ) {
			$structure['fixtures'][ $filename ] = file_get_contents( $fixtures_dir . $filename ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Valid edge case.
		}

		// Set up the "compiled" directory's virtual filesystem.
		$this->mock_filesystem = vfsStream::setup( 'compiled', 0755, $structure );

		// Set the fixture file dates back a week.
		$fixtures_dir           = $this->mock_filesystem->getChild( 'fixtures' );
		$file_modification_time = time() - ( 7 * 24 * 60 * 60 );
		foreach ( $filenames as $filename ) {
			$fixtures_dir->getChild( $filename )->lastModified( $file_modification_time );
		}
	}

	/**
	 * Add the virtual directory to the filesystem.
	 *
	 * @since 1.5.0
	 *
	 * @param string $dir_name Directory name.
	 * @param string $root_dir Optional. Root directory(ies) for the new directory.
	 *
	 * @return void
	 */
	protected function add_virtual_directory( $dir_name, $root_dir = 'compiled/beans/compiler' ) {
		vfsStream::newDirectory( $dir_name )->at( $this->mock_filesystem->getChild( $root_dir ) );
	}

	/**
	 * Create a file in the virtual directory system.
	 *
	 * @since 1.0.0
	 *
	 * @param string $folder_name Name of the folder to create, which is the configuration's ID.
	 * @param string $filename    File's name.
	 * @param string $content     The content to store in the file.
	 *
	 * @return string
	 */
	protected function create_virtual_file( $folder_name, $filename, $content ) {
		$this->add_virtual_directory( $folder_name );
		vfsStream::newFile( $filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $folder_name ) )
			->setContent( $content );

		return vfsStream::url( 'compiled/beans/compiler/' . $folder_name . '/' . $filename );
	}

	/**
	 * Set the protected property "current_fragment".
	 *
	 * @since 1.5.0
	 *
	 * @param \_Beans_Compiler $compiler The Compiler instance.
	 * @param mixed            $fragment The given value to set.
	 *
	 * @return \ReflectionProperty|string
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function set_current_fragment( $compiler, $fragment ) {
		return $this->set_reflective_property( $fragment, 'current_fragment', $compiler );
	}

	/**
	 * Get the filename.
	 *
	 * @since 1.5.0
	 *
	 * @param \_Beans_Compiler $compiler  Instance of the compiler.
	 * @param array            $config    The compiler's configuration.
	 * @param int              $filemtime Optional. The fragment's filemtime. Default is null.
	 *
	 * @return string
	 */
	protected function get_filename( $compiler, $config, $filemtime = null ) {

		if ( is_null( $filemtime ) ) {
			foreach ( $config['fragments'] as $index => $fragment ) {
				$filemtimes[ $index ] = filemtime( $fragment );
			}
		} else {
			$filemtimes = array( $filemtime );
		}

		return sprintf(
			'%s-%s.%s',
			$compiler->hash( $config ),
			$compiler->hash( $filemtimes ),
			'style' === $config['type'] ? 'css' : 'js'
		);
	}

	/**
	 * Set up the filesystem mocks for the fragments.
	 *
	 * @since 1.5.0
	 *
	 * @param _Beans_Compiler $compiler     Instance of the Compiler.
	 * @param int             $times_called Optional. Number of times the mock will be called. Default is 1.
	 *
	 * @return void
	 */
	protected function mock_filesystem_for_fragments( $compiler, $times_called = 1 ) {
		$mock = Mockery::mock( 'WP_Filesystem_Direct' );

		foreach ( $compiler->config['fragments'] as $fragment ) {
			$mock->shouldReceive( 'get_contents' )
				->times( $times_called )
				->with( $fragment )
				->andReturn( file_get_contents( $fragment ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Valid in this edge case.
		}

		$GLOBALS['wp_filesystem'] = $mock; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Valid use case as we are mocking the filesystem.
	}

	/**
	 * Mock the site's development mode.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $is_enabled Optional. When true, development mode is enabled. Default is false.
	 *
	 * @return void
	 */
	protected function mock_dev_mode( $is_enabled = false ) {
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_dev_mode', false )
			->andReturn( $is_enabled );
	}

	/**
	 * Get the compiled jQuery.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function get_compiled_jquery() {
		$compiled_content = <<<EOB
(function($){'use strict';var init=function(){/$('some-button').on('click',clickHandler);}
var clickHandler=function(event){event.preventDefault();}
$(document).ready(function(){init();});})(jQuery);
EOB;

		return str_replace( '/$', '$', $compiled_content );
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
body{background-color:#fff;color:#000;font-size:18px;
}
a{color:#cc0000;
}
p{margin-bottom:30px;
}
EOB;
	}

	/**
	 * Get the compiled LESS.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function get_compiled_less() {
		return <<<EOB
body{background-color:#fff;color:#000;font-size:18px;
}
EOB;
	}
}
