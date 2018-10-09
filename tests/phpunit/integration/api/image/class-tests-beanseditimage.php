<?php
/**
 * Tests for beans_edit_image().
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image;

use Beans\Framework\Tests\Integration\API\Image\Includes\Image_Test_Case;

require_once __DIR__ . '/includes/class-image-test-case.php';

/**
 * Class Tests_BeansEditImage
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansEditImage extends Image_Test_Case {

	/**
	 * Path of the fixtures directory.
	 *
	 * @var string
	 */
	protected static $fixtures_dir;

	/**
	 * Set up the test fixture before we start.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$fixtures_dir = realpath( __DIR__ . '/../fixtures' );
	}

	/**
	 * Test beans_edit_image() should return original src when the image does not exist.
	 */
	public function test_should_return_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame( $src, beans_edit_image( $src, [ 'resize' => [ 800, false ] ] ) );
	}

	/**
	 * Test beans_edit_image() should return an indexed array with the original src when the image does not exist.
	 */
	public function test_should_return_indexed_array_with_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame(
			[ $src, null, null ],
			beans_edit_image( $src, [ 'resize' => [ 800, false ] ], ARRAY_N )
		);
	}

	/**
	 * Test beans_edit_image() should return an object with the original src when the image does not exist.
	 */
	public function test_should_return_object_with_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Run the tests.
		$this->assertFileNotExists( $src );
		$image_info = beans_edit_image( $src, [ 'resize' => [ 800, false ] ], OBJECT );
		$this->assertInstanceOf( 'stdClass', $image_info );
		$this->assertSame( $src, $image_info->src );
		$this->assertNull( $image_info->width );
		$this->assertNull( $image_info->height );
	}

	/**
	 * Test beans_edit_image() should return an associative array with the original src when the image does not exist.
	 */
	public function test_should_return_associative_array_with_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame(
			[
				'src'    => $src,
				'width'  => null,
				'height' => null,
			],
			beans_edit_image( $src, [ 'resize' => [ 800, false ] ], ARRAY_A )
		);
	}
}
