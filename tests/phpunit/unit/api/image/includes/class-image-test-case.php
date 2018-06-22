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

use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Image_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Image\Includes
 */
abstract class Image_Test_Case extends Base_Test_Case {

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

		foreach ( [ 'ARRAY_N', 'ARRAY_A', 'STRING', 'OBJECT' ] as $constant ) {
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

		$this->setup_function_mocks();

		$this->load_original_functions( [
			'api/image/class-beans-image-editor.php',
		] );

		$this->images = [
			$this->images_dir . '/image1.jpg' => static::$fixtures_dir . '/image1.jpg',
			$this->images_dir . '/image2.jpg' => static::$fixtures_dir . '/image2.jpg',
		];
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

		$rebuilt_path->setValue( $editor, $path );

		return $rebuilt_path->getValue( $editor );
	}

	/**
	 * Set up the mocks.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	protected function setup_function_mocks() {
		Monkey\Functions\when( 'beans_url_to_path' )->returnArg();
		Monkey\Functions\when( 'beans_path_to_url' )->returnArg();

		Monkey\Functions\expect( 'wp_upload_dir' )->andReturn( [
			'path'    => '',
			'url'     => '',
			'subdir'  => '',
			'basedir' => vfsStream::url( 'uploads' ),
			'baseurl' => $this->images_url,
			'error'   => false,
		] );
	}
}
