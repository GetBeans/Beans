<?php
/**
 * Tests for the run() method of Beans_Image_Editor.
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
 * Class Tests_BeansImageEditor_Run
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansImageEditor_Run extends Image_Test_Case {

	/**
	 * Set up the test fixture before we start.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$fixtures_dir = realpath( __DIR__ . '/../fixtures' );
	}

	/**
	 * Test Beans_Image_Editor::run() should edit the existing image, store it in the "rebuilt path", and then return its URL.
	 */
	public function test_should_edit_store_image_and_return_its_url() {
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 800, false ] ];

		foreach ( $this->images as $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Simulate the WordPress' Editor.
			$wp_editor = Mockery::mock( 'WP_Image_Editor' );
			$wp_editor->shouldReceive( 'resize' )->once()->with( $args['resize'][0], $args['resize'][1] );
			$wp_editor->shouldReceive( 'save' )
				->once()
				->with( $edited_image_src )
				->andReturnUsing(
					function( $edited_image_src ) use ( $actual_path ) {
							imagejpeg( imagecreatefromjpeg( $actual_path ), $edited_image_src );
					}
				);
			Monkey\Functions\expect( 'wp_get_image_editor' )->with( $actual_path )->once()->andReturn( $wp_editor );
			Monkey\Functions\when( 'is_wp_error' )->justReturn( false );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( $edited_image_src, $image_info );
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should return original src when the image does not exist.
	 */
	public function test_should_return_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new Beans_Image_Editor( $src, [ 'resize' => [ 800, false ] ] );

		// Set up the mocks.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_path_to_url' )->never();

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame( $src, $editor->run() );
	}

	/**
	 * Test Beans_Image_Editor::run() should return the URL when the edited image exists, meaning that it has already been edited and
	 * stored.
	 */
	public function test_should_return_url_when_edited_image_exists() {
		$this->load_images_into_vfs();
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 800, false ] ];

		foreach ( $this->images as $virtual_path => $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor, $virtual_path );

			// Check that the WordPress Editor does not get called.
			Monkey\Functions\expect( 'wp_get_image_editor' )->never();
			Monkey\Functions\expect( 'is_wp_error' )->never();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( $edited_image_src, $editor->run() );
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should edit the existing image, store it in the "rebuilt path", and then return an indexed array of
	 * its image info.
	 */
	public function test_should_edit_store_image_and_return_indexed_array() {
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 800, false ] ];

		foreach ( $this->images as $virtual_path => $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args, ARRAY_N );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Simulate the WordPress' Editor.
			$wp_editor = Mockery::mock( 'WP_Image_Editor' );
			$wp_editor->shouldReceive( 'resize' )->once()->with( $args['resize'][0], $args['resize'][1] );
			$wp_editor->shouldReceive( 'save' )
				->once()
				->with( $edited_image_src )
				->andReturnUsing(
					function( $edited_image_src ) use ( $actual_path ) {
							imagejpeg( imagecreatefromjpeg( $actual_path ), $edited_image_src );
					}
				);
			Monkey\Functions\expect( 'wp_get_image_editor' )->with( $actual_path )->once()->andReturn( $wp_editor );
			Monkey\Functions\when( 'is_wp_error' )->justReturn( false );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( [ $edited_image_src, 1200, 630 ], $image_info );
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should return an indexed array with the original src when the image does not exist.
	 */
	public function test_should_return_indexed_array_with_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new Beans_Image_Editor( $src, [ 'resize' => [ 800, false ] ], ARRAY_N );

		// Set up the mocks.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_path_to_url' )->never();

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame( [ $src, null, null ], $editor->run() );
	}

	/**
	 * Test Beans_Image_Editor::run() should return return an indexed array when the edited image exists, meaning that it has already been
	 * edited and stored.
	 */
	public function test_should_return_index_array_when_edited_image_exists() {
		$this->load_images_into_vfs();
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 800, false ] ];

		foreach ( $this->images as $virtual_path => $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args, ARRAY_N );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor, $virtual_path );

			// Check that the WordPress Editor does not get called.
			Monkey\Functions\expect( 'wp_get_image_editor' )->never();
			Monkey\Functions\expect( 'is_wp_error' )->never();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$this->assertSame( [ $edited_image_src, 1200, 630 ], $editor->run() );
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should edit the existing image, store it in the "rebuilt path", and then return its image info as an
	 * object.
	 */
	public function test_should_edit_store_image_and_return_object() {
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 400, false ] ];

		foreach ( $this->images as $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args, OBJECT );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Simulate the WordPress' Editor.
			$wp_editor = Mockery::mock( 'WP_Image_Editor' );
			$wp_editor->shouldReceive( 'resize' )->once()->with( $args['resize'][0], $args['resize'][1] );
			$wp_editor->shouldReceive( 'save' )
				->once()
				->with( $edited_image_src )
				->andReturnUsing(
					function( $edited_image_src ) use ( $actual_path ) {
							imagejpeg( imagecreatefromjpeg( $actual_path ), $edited_image_src );
					}
				);
			Monkey\Functions\expect( 'wp_get_image_editor' )->with( $actual_path )->once()->andReturn( $wp_editor );
			Monkey\Functions\when( 'is_wp_error' )->justReturn( false );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertInstanceOf( 'stdClass', $image_info );
			$this->assertSame( $edited_image_src, $image_info->src );
			$this->assertSame( 1200, $image_info->width );
			$this->assertSame( 630, $image_info->height );
			$this->assertFileExists( $edited_image_src );
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should return an object with the original src when the image does not exist.
	 */
	public function test_should_return_object_with_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new Beans_Image_Editor( $src, [ 'resize' => [ 800, false ] ], OBJECT );

		// Set up the mocks.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_path_to_url' )->never();

		// Run the tests.
		$this->assertFileNotExists( $src );
		$image_info = $editor->run();
		$this->assertInstanceOf( 'stdClass', $image_info );
		$this->assertSame( $src, $image_info->src );
		$this->assertNull( $image_info->width );
		$this->assertNull( $image_info->height );
	}

	/**
	 * Test Beans_Image_Editor::run() should return an object when the edited image exists, meaning that it has already been
	 * edited and stored.
	 */
	public function test_should_return_object_when_edited_image_exists() {
		$this->load_images_into_vfs();
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 400, false ] ];

		foreach ( $this->images as $virtual_path => $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args, OBJECT );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor, $virtual_path );

			// Check that the WordPress Editor does not get called.
			Monkey\Functions\expect( 'wp_get_image_editor' )->never();
			Monkey\Functions\expect( 'is_wp_error' )->never();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$actual = $editor->run();
			$this->assertInstanceOf( 'stdClass', $actual );
			$this->assertSame( $edited_image_src, $actual->src );
			$this->assertSame( 1200, $actual->width );
			$this->assertSame( 630, $actual->height );
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should edit the existing image, store it in the "rebuilt path", and then return its image info as an
	 * associative array.
	 */
	public function test_should_edit_store_image_and_return_associative_array() {
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 600, false ] ];

		foreach ( $this->images as $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args, ARRAY_A );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Simulate the WordPress' Editor.
			$wp_editor = Mockery::mock( 'WP_Image_Editor' );
			$wp_editor->shouldReceive( 'resize' )->once()->with( $args['resize'][0], $args['resize'][1] );
			$wp_editor->shouldReceive( 'save' )
				->once()
				->with( $edited_image_src )
				->andReturnUsing(
					function( $edited_image_src ) use ( $actual_path ) {
							imagejpeg( imagecreatefromjpeg( $actual_path ), $edited_image_src );
					}
				);
			Monkey\Functions\expect( 'wp_get_image_editor' )->with( $actual_path )->once()->andReturn( $wp_editor );
			Monkey\Functions\when( 'is_wp_error' )->justReturn( false );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$image_info = $editor->run();
			$this->assertFileExists( $edited_image_src );
			$this->assertSame(
				[
					'src'    => $edited_image_src,
					'width'  => 1200,
					'height' => 630,
				],
				$image_info
			);
		}
	}

	/**
	 * Test Beans_Image_Editor::run() should return an associative array with the original src when the image does not exist.
	 */
	public function test_should_return_associative_array_with_original_src_when_no_image() {
		$src    = 'path/does/not/exist/image.jpg';
		$editor = new Beans_Image_Editor( $src, [ 'resize' => [ 800, false ] ], ARRAY_A );

		// Set up the mocks.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_path_to_url' )->never();

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame(
			[
				'src'    => $src,
				'width'  => null,
				'height' => null,
			],
			$editor->run()
		);
	}

	/**
	 * Test Beans_Image_Editor::run() should return an associative array when the edited image exists, meaning that it has already
	 * been edited and stored.
	 */
	public function test_should_return_associative_array_when_edited_image_exists() {
		$this->load_images_into_vfs();
		$rebuilt_path = $this->get_reflective_property( 'rebuilt_path', 'Beans_Image_Editor' );
		$args         = [ 'resize' => [ 600, false ] ];

		foreach ( $this->images as $virtual_path => $actual_path ) {
			$editor           = new Beans_Image_Editor( $actual_path, $args, ARRAY_A );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor, $virtual_path );

			// Check that the WordPress Editor does not get called.
			Monkey\Functions\expect( 'wp_get_image_editor' )->never();
			Monkey\Functions\expect( 'is_wp_error' )->never();

			// Run the tests.
			$this->assertFileExists( $edited_image_src );
			$this->assertSame(
				[
					'src'    => $edited_image_src,
					'width'  => 1200,
					'height' => 630,
				],
				$editor->run()
			);
		}
	}
}
