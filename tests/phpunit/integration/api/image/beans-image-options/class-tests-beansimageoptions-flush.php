<?php
/**
 * Tests for the flush() method of Beans_Image_Options.
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image;

use Beans_Image_Options;
use Beans\Framework\Tests\Integration\API\Image\Includes\Options_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansImageOptions_Flush
 *
 * @package Beans\Framework\Tests\Integration\API\Image
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

		$this->go_to_settings_page();
		$_POST['beans_flush_edited_images'] = 1;

		// Return the virtual filesystem's path to avoid wp_normalize_path converting its prefix from vfs::// to vfs:/.
		Monkey\Functions\when( 'wp_normalize_path' )->returnArg();

		$this->assertNull( ( new Beans_Image_Options() )->flush() );
		$this->assertDirectoryNotExists( vfsStream::url( 'uploads/beans/images/' ) );
	}
}
