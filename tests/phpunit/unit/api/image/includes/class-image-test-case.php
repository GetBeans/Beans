<?php
/**
 * Tests Case for Beans' Image API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Image\Includes
 *
 * @since   1.5.0
 *
 * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found -- Valid use cases to minimize work in tests.
 */

namespace Beans\Framework\Tests\Unit\API\Image\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Image_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Image\Includes
 */
abstract class Image_Test_Case extends Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Path to the images' directory.
	 *
	 * @var string
	 */
	protected $images_dir;

	/**
	 * Path to the images directory's URL.
	 *
	 * @var string
	 */
	protected $images_url;

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
	 * Array of images.
	 *
	 * @var array
	 */
	protected $images;

	/**
	 * Path of the fixtures directory.
	 *
	 * @var string
	 */
	protected static $fixtures_dir;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$fixtures_dir = __DIR__ . '/fixtures';

		foreach ( array( 'ARRAY_N', 'ARRAY_A', 'STRING', 'OBJECT' ) as $constant ) {
			if ( ! defined( $constant ) ) {
				define( $constant, $constant ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress defined constants.
			}
		}
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/image/functions.php',
			'api/image/class-beans-image-editor.php',
			'api/utilities/functions.php',
		) );

		$this->set_up_virtual_filesystem();

		$this->setup_function_mocks();

		$this->images = array(
			$this->images_dir . '/image1.jpg' => static::$fixtures_dir . '/image1.jpg',
			$this->images_dir . '/image2.jpg' => static::$fixtures_dir . '/image2.jpg',
		);
	}

	/**
	 * Set up the virtual filesystem.
	 */
	private function set_up_virtual_filesystem() {
		$structure = array(
			'beans' => array(
				'images' => array(
					'index.php' => '',
				),
			),
		);

		// Set up the "beans" directory's virtual filesystem.
		$this->mock_filesystem = vfsStream::setup( 'uploads', 0755, $structure );
		$this->images_dir      = vfsStream::url( 'uploads/beans/images' );
		$this->images_url      = 'http://example.com/uploads/beans/images/';
	}

	/**
	 * Load the images into the virtual filesystem.
	 */
	protected function load_images_into_vfs() {

		foreach ( $this->images as $virtual_path => $actual_path ) {
			imagejpeg( imagecreatefromjpeg( $actual_path ), $virtual_path );
		}
	}

	/**
	 * Initialize the virtual "edited" image.
	 *
	 * @since 1.5.0
	 *
	 * @param \ReflectionProperty $rebuilt_path Instance of the editor's "rebuilt path" property.
	 * @param _Beans_Image_Editor $editor       Instance of the editor.
	 * @param string|null         $path         Optional. The image's "rebuilt path".
	 *
	 * @return string
	 */
	protected function init_virtual_image( $rebuilt_path, $editor, $path = null ) {

		if ( is_null( $path ) ) {
			$path = $rebuilt_path->getValue( $editor );
		}

		$path = $this->fix_virtual_dir( $path );
		$rebuilt_path->setValue( $editor, $path );

		return $rebuilt_path->getValue( $editor );
	}

	/**
	 * Fix the virtual directory. Modify the root, as wp_normalize_path changes it.
	 *
	 * @since 1.5.0
	 *
	 * @param string $path The path to fix.
	 *
	 * @return string
	 */
	protected function fix_virtual_dir( $path ) {

		if ( substr( $path, 0, 6 ) === 'vfs://' ) {
			return $path;
		}

		return str_replace( 'vfs:/', 'vfs://', $path );
	}

	/**
	 * Removes the vfsStream's root, i.e. vfs:// or vfs:/.
	 *
	 * @since 1.5.0
	 *
	 * @param string $path The path to fix.
	 *
	 * @return string
	 */
	protected function remove_virtual_dir_root( $path ) {
		$pattern = substr( $path, 0, 6 ) === 'vfs://'
			? 'vfs://'
			: 'vfs:/';

		return str_replace( $pattern, '', $path );
	}

	/**
	 * Setup the mocks.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	protected function setup_function_mocks() {
		Monkey\Functions\when( 'beans_url_to_path' )->returnArg();
		Monkey\Functions\when( 'beans_path_to_url' )->returnArg();

		Monkey\Functions\expect( 'wp_upload_dir' )->andReturn( array(
			'path'    => '',
			'url'     => '',
			'subdir'  => '',
			'basedir' => vfsStream::url( 'uploads' ),
			'baseurl' => $this->images_url,
			'error'   => false,
		) );
	}
}
