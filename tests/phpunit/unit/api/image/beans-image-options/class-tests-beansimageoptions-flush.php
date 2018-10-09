<?php
/**
 * Tests for the flush() method of Beans_Image_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image;

use Beans_Image_Options;
use Beans\Framework\Tests\Unit\API\Image\Includes\Options_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansImageOptions_Flush
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansImageOptions_Flush extends Options_Test_Case {

	/**
	 * Test Beans_Image_Options::flush() should not remove the cached directory when this is not a 'edited images
	 * flush'.
	 */
	public function test_should_not_remove_cached_dir_when_not_a_flush() {
		// Check that the cached directory exists before we start.
		$this->directoryExists( vfsStream::url( 'uploads/beans/images/' ) );

		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_flush_edited_images' )
			->andReturnNull();
		Monkey\Functions\expect( 'beans_get_images_dir' )->never();
		Monkey\Functions\expect( 'beans_remove_dir' )->never();

		$this->assertNull( ( new Beans_Image_Options() )->flush() );

		// Check that it still exists and was not removed.
		$this->directoryExists( vfsStream::url( 'uploads/beans/images/' ) );
	}

	/**
	 * Test Beans_Image_Options::flush() should remove the cached directory.
	 */
	public function test_should_remove_cached_dir() {
		// Check that the cached directory exists before we start.
		$this->directoryExists( vfsStream::url( 'uploads/beans/images/' ) );

		Monkey\Functions\expect( 'beans_post' )
			->once()
			->with( 'beans_flush_edited_images' )
			->andReturnFirstArg();
		Monkey\Functions\expect( 'beans_get_images_dir' )
			->once()
			->andReturn( vfsStream::url( 'uploads/beans/images/' ) );
		Monkey\Functions\expect( 'beans_remove_dir' )
			->once()
			->with( vfsStream::url( 'uploads/beans/images/' ) )
			->andReturnUsing(
				function() {
						// Keep it simple. Remove by redefining.
						vfsStream::setup( 'uploads', 0755, [ 'beans' => [] ] );
				}
			);

		$this->assertNull( ( new Beans_Image_Options() )->flush() );
		$this->assertDirectoryNotExists( vfsStream::url( 'uploads/beans/images/' ) );
	}
}
