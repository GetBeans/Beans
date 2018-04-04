<?php
/**
 * Tests Case for Beans' Compiler API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler\Includes;

use WP_UnitTestCase;
use Mockery;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 */
abstract class Compiler_Test_Case extends WP_UnitTestCase {

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
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();
		$this->compiled_dir = vfsStream::url( 'compiled' );

		// Set the Uploads directory to our virtual filesystem.
		add_filter( 'upload_dir', function( array $uploads_dir ) {
			$uploads_dir['path']    = $this->compiled_dir . $uploads_dir['subdir'];
			$uploads_dir['url']     = str_replace( 'wp-content/uploads', 'compiled', $uploads_dir['url'] );
			$uploads_dir['basedir'] = $this->compiled_dir;
			$uploads_dir['baseurl'] = str_replace( 'wp-content/uploads', 'compiled', $uploads_dir['baseurl'] );

			return $uploads_dir;
		} );
	}

	/**
	 * Tear down the test fixture.
	 */
	public function tearDown() {
		unset( $GLOBALS['wp_filesystem'] );

		Mockery::close();
		parent::tearDown();
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
	 * Set the protected property "current_fragment".
	 *
	 * @since 1.5.0
	 *
	 * @param \_Beans_Compiler $compiler The Compiler instance.
	 * @param mixed            $fragment The given value to set.
	 *
	 * @return void
	 */
	protected function set_current_fragment( $compiler, $fragment ) {
		$current_fragment = ( new \ReflectionClass( $compiler ) )->getProperty( 'current_fragment' );
		$current_fragment->setAccessible( true );
		$current_fragment->setValue( $compiler, $fragment );
		$current_fragment->setAccessible( false );
	}

	/**
	 * Fix the compiler's "dir" property, as the wp_normalize_path() converts "vfs://" to "vfs:/".
	 *
	 * @since 1.5.0
	 *
	 * @param array $config Compiler's configuration.
	 *
	 * @return \_Beans_Compiler
	 */
	protected function create_compiler( $config ) {
		$compiler = new \_Beans_Compiler( $config );

		$dir = ( new \ReflectionClass( $compiler ) )->getProperty( 'dir' );
		$dir->setAccessible( true );

		if ( substr( $compiler->dir, 0, 6 ) !== 'vfs://' ) {
			$dir->setValue( $compiler, str_replace( 'vfs:/', 'vfs://', $compiler->dir ) );
		}

		$dir->setAccessible( false );
		return $compiler;
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

		$GLOBALS['wp_filesystem'] = $mock; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Valid use case, as we are mocking the filesystem.
	}

	/**
	 * Set Development Mode.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $is_enabled Optional. When true, turns on development mode. Default is false.
	 *
	 * @return void
	 */
	protected function set_dev_mode( $is_enabled = false ) {
		update_option( 'beans_dev_mode', $is_enabled );
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
