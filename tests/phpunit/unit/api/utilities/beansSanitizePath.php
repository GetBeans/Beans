<?php
/**
 * Tests for beans_sanitize_path()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansSanitizePath
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansSanitizePath extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_sanitize_path() should remove Windows drive.
	 */
	public function test_should_remove_windows_drive() {
		$this->assertSame( '/Program Files/tmp/', beans_sanitize_path( 'C:\Program Files\tmp\\' ) );
		$this->assertSame( '/Foo/bar/baz/index.txt', beans_sanitize_path( 'D:\Foo\bar\baz\index.txt' ) );
	}

	/**
	 * Test beans_sanitize_path() should sanitize for filesystem.
	 */
	public function test_should_sanitize_for_filesystem() {
		$this->assertSame( $this->prepare_path( BEANS_TESTS_DIR ), beans_sanitize_path( BEANS_TESTS_DIR ) );
		$this->assertSame( $this->prepare_path( __DIR__ ), beans_sanitize_path( __DIR__ ) );

		// Test phpunit's path.
		$this->assertSame(
			$this->prepare_path( BEANS_TESTS_DIR ) . '/bootstrap.php',
			beans_sanitize_path( BEANS_TESTS_DIR . DIRECTORY_SEPARATOR . 'bootstrap.php' )
		);
		$this->assertSame( $this->prepare_path( __FILE__ ), beans_sanitize_path( __FILE__ ) );

		// Test Beans' theme root path.
		$this->assertSame(
			$this->prepare_path( rtrim( BEANS_THEME_DIR, DIRECTORY_SEPARATOR ) ),
			beans_sanitize_path( BEANS_THEME_DIR )
		);
		$directory_separator = '\\' === DIRECTORY_SEPARATOR ? '/' : '';
		$this->assertSame(
			$this->prepare_path( BEANS_THEME_DIR ) . $directory_separator . 'functions.php',
			beans_sanitize_path( BEANS_THEME_DIR . 'functions.php' )
		);
	}

	/**
	 * Test beans_sanitize_path() should sanitize for filesystem symbolic links.
	 */
	public function test_should_sanitize_for_filesystem_symbolic_links() {
		// Test in the ./tests/phpunit/unit/api directory.
		$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
		$this->assertSame(
			$this->prepare_path( BEANS_TESTS_DIR ) . '/api',
			beans_sanitize_path( $path )
		);

		// Test in the ./tests/phpunit/unit directory.
		$path .= '..' . DIRECTORY_SEPARATOR;
		$this->assertSame(
			$this->prepare_path( BEANS_TESTS_DIR ),
			beans_sanitize_path( $path )
		);
		$this->assertSame(
			$this->prepare_path( BEANS_TESTS_DIR ) . '/bootstrap.php',
			beans_sanitize_path( $path . 'bootstrap.php' )
		);
	}

	/**
	 * Prepare the path for different OS environments.
	 *
	 * @since 1.5.0
	 *
	 * @param string $path Filesystem path to prepare.
	 *
	 * @return string
	 */
	protected function prepare_path( $path ) {

		// It's not a Windows path. Just return.
		if ( '\\' !== DIRECTORY_SEPARATOR ) {
			return $path;
		}

		$path = wp_normalize_path( realpath( $path ) );

		// Strips off the Windows drive letter.
		return mb_substr( $path, 2 );
	}
}
