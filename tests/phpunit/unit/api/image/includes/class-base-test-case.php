<?php
/**
 * The base Test Case for Beans' Image API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Image\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Base_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Image\Includes
 */
abstract class Base_Test_Case extends Test_Case {

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
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();

		$this->load_original_functions(
			[
				'api/utilities/functions.php',
				'api/image/functions.php',
			]
		);

		$this->setup_common_wp_stubs();
	}

	/**
	 * Set up the virtual filesystem.
	 */
	protected function set_up_virtual_filesystem() {
		$this->mock_filesystem = vfsStream::setup(
			'uploads',
			0755,
			$this->get_virtual_structure()
		);
		$this->images_dir      = vfsStream::url( 'uploads/beans/images' );
		$this->images_url      = 'http://example.com/uploads/beans/images/';
	}

	/**
	 * Get the virtual filesystem's structure.
	 */
	protected function get_virtual_structure() {
		return [
			'beans' => [
				'images' => [
					'index.php' => '',
				],
			],
		];
	}
}
