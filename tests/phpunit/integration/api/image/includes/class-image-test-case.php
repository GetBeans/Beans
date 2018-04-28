<?php
/**
 * Test Case for Beans' Image API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image\Includes;

use Beans\Framework\Tests\Integration\Test_Case;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Image_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
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
	 * Instance of vfsStreamDirectory to mock the filesystem.
	 *
	 * @var vfsStreamDirectory
	 */
	protected $mock_filesystem;

	/**
	 * Set up the test fixture.
	 */
	public function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();
		$this->images_dir = vfsStream::url( 'uploads/beans/images' );

		// Set the Uploads directory to our virtual filesystem.
		add_filter( 'upload_dir', function( array $uploads_dir ) {
			$virtual_dir            = vfsStream::url( 'uploads' );
			$uploads_dir['path']    = $virtual_dir . $uploads_dir['subdir'];
			$uploads_dir['basedir'] = $virtual_dir;
			return $uploads_dir;
		} );
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
}
