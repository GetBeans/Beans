<?php
/**
 * Tests for the create_edited_image() method of Beans_Image_Editor.
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image;

use Beans_Image_Editor;
use Beans\Framework\Tests\Unit\API\Image\Includes\Image_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-image-test-case.php';

/**
 * Class Tests_BeansImageEditor_CreateEditedImage
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansImageEditor_CreateEditedImage extends Image_Test_Case {

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
	 * Test Beans_Image_Editor::create_edited_image() should edit the given image and then create a new "edited image", which is stored in
	 * the "rebuilt path".
	 */
	public function test_should_edit_create_and_store_image() {
		$create_edited_image = $this->get_reflective_method( 'create_edited_image', 'Beans_Image_Editor' );
		$rebuilt_path        = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$image_sources       = [
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		];
		$args                = [ 'resize' => [ 800, false ] ];

		foreach ( $image_sources as $src ) {
			$editor           = new Beans_Image_Editor( $src, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Simulate the WordPress' Editor.
			$wp_editor = Mockery::mock( 'WP_Image_Editor' );
			$wp_editor->shouldReceive( 'resize' )->once()->with( $args['resize'][0], $args['resize'][1] );
			$wp_editor->shouldReceive( 'save' )
				->once()
				->with( $edited_image_src )
				->andReturnUsing(
					function( $edited_image_src ) use ( $src ) {
							imagejpeg( imagecreatefromjpeg( $src ), $edited_image_src );
					}
				);
			Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once()->andReturn( $wp_editor );
			Monkey\Functions\when( 'is_wp_error' )->justReturn( false );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$this->assertTrue( $create_edited_image->invoke( $editor ) );
			$this->assertFileExists( $edited_image_src );
		}
	}

	/**
	 * Test Beans_Image_Editor::create_edited_image() should return false when the image does not exist.
	 */
	public function test_should_return_false_when_no_image() {
		$create_edited_image = $this->get_reflective_method( 'create_edited_image', 'Beans_Image_Editor' );
		$rebuilt_path        = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$src                 = 'path/does/not/exist/image.jpg';
		$args                = [ 'resize' => [ 800, false ] ];

		$editor           = new Beans_Image_Editor( $src, $args );
		$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

		// Simulate the WordPress' Editor.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertFalse( $create_edited_image->invoke( $editor ) );
		$this->assertFileNotExists( $edited_image_src );
	}
}
