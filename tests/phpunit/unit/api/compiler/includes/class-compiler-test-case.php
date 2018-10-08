<?php
/**
 * Tests Case for Beans' Compiler API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler\Includes;

use Beans_Compiler;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 */
abstract class Compiler_Test_Case extends Base_Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Flag is in admin area (back-end).
	 *
	 * @var bool
	 */
	protected $is_admin = false;

	/**
	 * An array of fixture filenames.
	 *
	 * @var array
	 */
	protected static $fixture_filenames;

	/**
	 * The test fixtures directory.
	 *
	 * @var string
	 */
	protected static $fixtures_dir;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', 0644 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Valid constant.
		}

		static::$fixture_filenames = [
			'jquery.test.js',
			'my-game-clock.js',
			'style.css',
			'test.less',
			'variables.less',
		];
		static::$fixtures_dir      = basename( __DIR__ ) === 'compiler'
			? __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR
			: dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR;
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		$this->set_up_function_mocks();

		$this->load_original_functions(
			[
				'api/compiler/class-beans-compiler.php',
			]
		);
	}

	/**
	 * Set up the virtual filesystem.
	 */
	protected function set_up_virtual_filesystem() {
		parent::set_up_virtual_filesystem();

		// Set the fixture file dates back a week.
		$fixtures_dir           = $this->mock_filesystem->getChild( 'fixtures' );
		$file_modification_time = time() - ( 7 * 24 * 60 * 60 );
		foreach ( static::$fixture_filenames as $filename ) {
			$fixtures_dir->getChild( $filename )->lastModified( $file_modification_time );
		}
	}

	/**
	 * Get the virtual filesystem's structure.
	 */
	protected function get_virtual_structure() {
		$structure             = parent::get_virtual_structure();
		$structure['fixtures'] = $this->get_fixtures_content();

		return $structure;
	}

	/**
	 * Get the test fixture's content.
	 */
	private function get_fixtures_content() {
		$fixtures = [];

		foreach ( static::$fixture_filenames as $filename ) {
			$fixtures[ $filename ] = file_get_contents( static::$fixtures_dir . $filename ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Valid edge case.
		}

		return $fixtures;
	}

	/**
	 * Set up function mocks.
	 */
	protected function set_up_function_mocks() {
		Functions\when( 'wp_upload_dir' )->justReturn(
			[
				'path'    => '',
				'url'     => '',
				'subdir'  => '',
				'basedir' => $this->compiled_dir,
				'baseurl' => $this->compiled_url,
				'error'   => false,
			]
		);
		Functions\when( 'is_admin' )->justReturn( $this->is_admin );
		Functions\when( 'site_url' )->justReturn( 'http:://beans.local' );
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

		$cached_file = vfsStream::url( 'compiled/beans/compiler/' . $folder_name . '/' . $filename );

		// vfs has a little quirk: We need to check the file to finish storing it in the system.  This helps us to modify it later.
		file_exists( $cached_file );

		return $cached_file;
	}

	/**
	 * Create the compiler.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Compiler's configuration parameters.
	 *
	 * @return Beans_Compiler
	 */
	protected function create_compiler( array $config = [] ) {
		Monkey\Functions\when( 'beans_get_compiler_dir' )->justReturn( vfsStream::url( 'compiled/beans/compiler/' ) );
		Monkey\Functions\when( 'beans_get_compiler_url' )->justReturn( $this->compiled_url . 'beans/compiler/' );

		return new Beans_Compiler( $config );
	}

	/**
	 * Set the protected property "current_fragment".
	 *
	 * @since 1.5.0
	 *
	 * @param Beans_Compiler $compiler The Compiler instance.
	 * @param mixed           $fragment The given value to set.
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
	 * @param Beans_Compiler $compiler  Instance of the compiler.
	 * @param array           $config    The compiler's configuration.
	 * @param int             $filemtime Optional. The fragment's filemtime. Default is null.
	 *
	 * @return string
	 */
	protected function get_filename( $compiler, $config, $filemtime = null ) {

		if ( is_null( $filemtime ) ) {
			foreach ( $config['fragments'] as $index => $fragment ) {
				$filemtimes[ $index ] = filemtime( $fragment );
			}
		} else {
			$filemtimes = [ $filemtime ];
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
	 * @param Beans_Compiler $compiler     Instance of the Compiler.
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

		$GLOBALS['wp_filesystem'] = $mock; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case as we are mocking the filesystem.
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
body{background-color:#fff;color:#000;font-size:18px}
a{color:#cc0000}
p{margin-bottom:30px}
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
body{background-color:#fff;color:#000;font-size:18px}
EOB;
	}

	/**
	 * Strip out the non-essential characters for cross-platform testing.
	 *
	 * @param string $string The string to be processed.
	 *
	 * @return string
	 */
	protected function strip_characters( $string ) {
		return str_replace( [ "\r\n", "\r", "\n", "\t", ' ' ], '', $string );
	}
}
