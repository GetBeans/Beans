<?php
/**
 * Tests for beans_remove_dir()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;
use org\bovigo\vfs\vfsStream;

/**
 * Class Tests_BeansRemoveDir
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansRemoveDir extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_remove_dir() should bail out for a non-directory.
	 */
	public function test_should_bail_for_non_dir() {
		$dir = __DIR__ . '/';

		$this->assertFalse( beans_remove_dir( $dir . 'this-dir-does-not-exist' ) );
		$this->assertFalse( beans_remove_dir( $dir . 'foo' ) );
		$this->assertFalse( beans_remove_dir( __FILE__ ) );
	}

	/**
	 * Test beans_remove_dir() should remove a directory without files.
	 */
	public function test_should_remove_dir_with_no_files() {
		vfsStream::setup( 'remove-dir' );
		$dir = vfsStream::url( 'remove-dir' );

		$this->directoryExists( $dir );
		$this->assertTrue( beans_remove_dir( $dir ) );
		$this->assertDirectoryNotExists( $dir );

		vfsStream::setup( 'remove-dir', 0644 );
		$this->directoryExists( $dir );
		$this->assertTrue( beans_remove_dir( $dir ) );
		$this->assertDirectoryNotExists( $dir );
	}

	/**
	 * Test beans_remove_dir() should remove a directory with files.
	 */
	public function test_should_remove_dir_with_files() {
		vfsStream::setup( 'remove-dir', 0644, [
			'foo.txt' => 'Testing Beans for foo.txt',
			'bar.txt' => 'Testing Beans for bar.txt',
		] );
		$dir = vfsStream::url( 'remove-dir' );

		$this->directoryExists( $dir );
		$this->assertFileExists( $dir . '/foo.txt' );
		$this->assertFileExists( $dir . '/bar.txt' );
		$this->assertTrue( beans_remove_dir( $dir ) );
		$this->assertDirectoryNotExists( $dir );
		$this->assertFileNotExists( $dir . '/foo.txt' );
		$this->assertFileNotExists( $dir . '/bar.txt' );
	}

	/**
	 * Test beans_remove_dir() should remove a deeply nested directory.
	 */
	public function test_should_remove_deeply_nested_dir() {
		vfsStream::setup( 'remove-dir', 0644, [
			'foo.txt' => 'Testing Beans for foo.txt',
			'sub-dir' => [
				'bar.txt'     => 'Testing Beans for bar.txt',
				'sub-sub-dir' => [
					'baz.log'    => 'Baz logger',
					'foobar.log' => 'Foobar logger',
				],
			],
		] );
		$dir = vfsStream::url( 'remove-dir' );

		$this->directoryExists( $dir );
		$this->assertFileExists( $dir . '/foo.txt' );

		$this->directoryExists( $dir . '/sub-dir' );
		$this->assertFileExists( $dir . '/sub-dir/bar.txt' );

		$this->directoryExists( $dir . '/sub-dir/sub-sub-dir' );
		$this->assertFileExists( $dir . '/sub-dir/sub-sub-dir/baz.log' );
		$this->assertFileExists( $dir . '/sub-dir/sub-sub-dir/foobar.log' );

		$this->assertTrue( beans_remove_dir( $dir ) );

		$this->assertDirectoryNotExists( $dir );
		$this->assertFileNotExists( $dir . '/foo.txt' );

		$this->assertDirectoryNotExists( $dir . '/sub-dir' );
		$this->assertFileNotExists( $dir . '/sub-dir/bar.txt' );

		$this->assertDirectoryNotExists( $dir . '/sub-dir/sub-sub-dir' );
		$this->assertFileNotExists( $dir . '/sub-dir/sub-sub-dir/baz.log' );
		$this->assertFileNotExists( $dir . '/sub-dir/sub-sub-dir/foobar.log' );
	}
}
