<?php
/**
 * Test Case for Beans' Image API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image\Includes;

use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Image_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 */
abstract class Image_Test_Case extends Base_Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Return the virtual filesystem's path to avoid wp_normalize_path converting its prefix from vfs::// to vfs:/.
		Monkey\Functions\when( 'wp_normalize_path' )->returnArg();
	}

	/**
	 * Initialize the virtual "edited" image.
	 *
	 * @since 1.5.0
	 *
	 * @param \ReflectionProperty $rebuilt_path Instance of the editor's "rebuilt path" property.
	 * @param Beans_Image_Editor $editor       Instance of the editor.
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
}
