<?php
/**
 * Tests Case for Beans' Compiler API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler\Includes;

use Beans_Compiler;
use Brain\Monkey;
use Mockery;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 */
abstract class Compiler_Test_Case extends Base_Test_Case {

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

		set_current_screen( 'front' );

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
	public function setUp() {
		parent::setUp();

		// Set up the global fragments container.
		global $_beans_compiler_added_fragments;
		$_beans_compiler_added_fragments = [
			'css'  => [],
			'less' => [],
			'js'   => [],
		];

		// Return the virtual filesystem's path to avoid wp_normalize_path converting its prefix from vfs::// to vfs:/.
		Monkey\Functions\when( 'wp_normalize_path' )->returnArg();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		wp_dequeue_script( 'test-jquery' );
		unset( $GLOBALS['wp_scripts']->registered['test-jquery'] );
		wp_dequeue_script( 'test-script' );
		unset( $GLOBALS['wp_scripts']->registered['test-script'] );
		wp_dequeue_script( 'test-js' );
		unset( $GLOBALS['wp_scripts']->registered['test-js'] );
		wp_dequeue_style( 'test-css' );
		unset( $GLOBALS['wp_styles']->registered['test-css'] );
		unset( $GLOBALS['wp_filesystem'] );
		parent::tearDown();
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
	 * Set the protected property "current_fragment".
	 *
	 * @since 1.5.0
	 *
	 * @param Beans_Compiler $compiler The Compiler instance.
	 * @param mixed           $fragment The given value to set.
	 *
	 * @return void
	 * @throws \ReflectionException Throws reflection error.
	 */
	protected function set_current_fragment( $compiler, $fragment ) {
		$current_fragment = ( new \ReflectionClass( $compiler ) )->getProperty( 'current_fragment' );
		$current_fragment->setAccessible( true );
		$current_fragment->setValue( $compiler, $fragment );
		$current_fragment->setAccessible( false );
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
	 * @param string          $class        Mock's class name.
	 *
	 * @return void
	 */
	protected function mock_filesystem_for_fragments(
		$compiler,
		$times_called = 1,
		$class = 'WP_Filesystem_Direct_Mock'
	) {

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', 0644 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Valid constant.
		}

		unset( $GLOBALS['wp_filesystem'] );

		// Now set up the mock.
		$mock = Mockery::mock( $class );

		foreach ( $compiler->config['fragments'] as $fragment ) {
			$mock->shouldReceive( 'get_contents' )
				->times( $times_called )
				->with( $fragment )
				->andReturn( file_get_contents( $fragment ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents -- Valid in this edge case.
		}

		$GLOBALS['wp_filesystem'] = $mock; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case, as we are mocking the filesystem.
	}

	/**
	 * Get the file's content.
	 *
	 * @since 1.5.0
	 *
	 * @param string $filename  Name of the file.
	 * @param string $id File's ID.
	 *
	 * @return string
	 */
	protected function get_cached_contents( $filename, $id ) {
		return $this->mock_filesystem
			->getChild( 'beans/compiler/' . $id )
			->getChild( $filename )
			->getContent();
	}

	/**
	 * Get the compiled file's name.
	 *
	 * @param string $path The virtual filesystem's path.
	 *
	 * @return string
	 */
	protected function get_compiled_filename( $path ) {
		$files = beans_scandir( $path );

		if ( empty( $files ) ) {
			return '';
		}

		return array_pop( $files );
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
}
