<?php
/**
 * Test Case for Beans' UIkit API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit\Includes;

use Beans\Framework\Tests\Integration\Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class UIkit_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit\Includes
 */
abstract class UIkit_Test_Case extends Test_Case {

	/**
	 * Instance of vfsStreamDirectory to mock the filesystem.
	 *
	 * @var vfsStreamDirectory
	 */
	protected $mock_filesystem;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();
		$this->set_up_mocks();
		$this->set_up_uploads_directory();

		$this->reset_globals();
	}

	/**
	 * Set up the virtual filesystem.
	 */
	protected function set_up_virtual_filesystem() {
		$this->mock_filesystem = vfsStream::setup(
			'virtual-wp-content',
			0755,
			$this->get_virtual_structure()
		);
	}

	/**
	 * Get the virtual filesystem's structure.
	 */
	protected function get_virtual_structure() {
		return [
			'themes'  => [
				'beans-child' => [
					'assets'        => [
						'js'   => [
							'alert.min.js' => '',
						],
						'less' => [
							'theme' => [
								'alert.less'     => '',
								'article.less'   => '',
								'panel.less'     => '',
								'variables.less' => '',
								'style.less'     => '',
								'variables.less' => '',
							],
						],
					],
					'functions.php' => '',
					'style.css'     => '',
				],
			],
			'uploads' => [
				'beans' => [
					'compiler' => [
						'uikit' => [],
					],
				],
			],
		];
	}

	/**
	 * Set up the mocks.
	 */
	protected function set_up_mocks() {
		// Return the virtual filesystem's path to avoid wp_normalize_path converting its prefix from vfs::// to vfs:/.
		Monkey\Functions\when( 'wp_normalize_path' )->returnArg();
	}

	/**
	 * Set up the Uploads directory to our virtual filesystem.
	 */
	protected function set_up_uploads_directory() {
		add_filter( 'upload_dir', function( array $uploads_dir ) {
			$compiled_dir = vfsStream::url( 'virtual-wp-content/uploads' );

			$uploads_dir['path']    = $compiled_dir . $uploads_dir['subdir'];
			$uploads_dir['url']     = str_replace( 'wp-content/uploads', 'compiled', $uploads_dir['url'] );
			$uploads_dir['basedir'] = $compiled_dir;
			$uploads_dir['baseurl'] = str_replace( 'wp-content/uploads', 'compiled', $uploads_dir['baseurl'] );

			return $uploads_dir;
		} );
	}

	/**
	 * Reset the global containers.
	 */
	protected function reset_globals() {
		global $_beans_uikit_enqueued_items, $_beans_uikit_registered_items;

		$_beans_uikit_enqueued_items = [
			'components' => [
				'core'    => [],
				'add-ons' => [],
			],
			'themes'     => [],
		];

		$_beans_uikit_registered_items = [
			'themes' => [
				'default'         => BEANS_API_PATH . 'uikit/src/themes/default',
				'almost-flat'     => BEANS_API_PATH . 'uikit/src/themes/almost-flat',
				'gradient'        => BEANS_API_PATH . 'uikit/src/themes/gradient',
				'wordpress-admin' => BEANS_API_PATH . 'uikit/themes/wordpress-admin',
			],
		];
	}

	/**
	 * Get the file's content.
	 *
	 * @since 1.5.0
	 *
	 * @param string $filename Name of the file.
	 * @param string $id       File's ID.
	 *
	 * @return string
	 */
	protected function get_cached_contents( $filename, $id ) {
		return $this->mock_filesystem
			->getChild( 'virtual-wp-content/uploads/beans/compiler/' . $id )
			->getChild( $filename )
			->getContent();
	}

	/**
	 * Get the compiled file's name.
	 *
	 * @param string $path The virtual filesystem's path.
	 *
	 * @return string
	 */
	protected function get_compiled_filename( $path ) {
		$files = beans_scandir( $path );

		if ( empty( $files ) ) {
			return '';
		}

		return array_pop( $files );
	}
}
