<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Beans\BeansFramework\Tests\BeansTestCase;

class RenderFunctionTest extends BeansTestCase {

	protected $dir;

	protected function setUp() {
		parent::setUp();
		$this->dir = dirname( __FILE__ ) . '/removeDir';
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	protected function tearDown() {
		parent::tearDown();
		if ( is_dir( $this->dir ) ) {
			if ( ! is_writable( $this->dir ) ) {
				chmod( $this->dir, 0755 );
			}
			rmdir( $this->dir );
		}
	}

	public function testBailsForNonDir() {
		$dir = dirname( __FILE__ ) . '/';
		$this->assertFalse( beans_remove_dir( $dir . 'this-dir-doesnot-exist' ) );
		$this->assertFalse( beans_remove_dir( $dir . 'foo' ) );
		$this->assertFalse( beans_remove_dir( __FILE__ ) );
	}

	public function testRemovesWithNoFiles() {
		mkdir( $this->dir );

		$this->directoryExists( $this->dir );
		$this->assertTrue( beans_remove_dir( $this->dir ) );
		$this->assertDirectoryNotExists( $this->dir );

		mkdir( $this->dir, 0644 );

		$this->directoryExists( $this->dir );
		$this->assertTrue( beans_remove_dir( $this->dir ) );
		$this->assertDirectoryNotExists( $this->dir );
	}

	public function testRemovesWithFiles() {
		// Setup by creating the directory and a couple of files.
		mkdir( $this->dir );

		$handle = fopen( $this->dir . '/foo.txt', 'w' );
		fwrite( $handle, 'Testing Beans' );
		fclose( $handle );
		copy( $this->dir . '/foo.txt', $this->dir . '/bar.txt' );

		$this->directoryExists( $this->dir );
		$this->assertFileExists( $this->dir . '/foo.txt' );
		$this->assertFileExists( $this->dir . '/bar.txt' );
		$this->assertTrue( beans_remove_dir( $this->dir ) );
		$this->assertDirectoryNotExists( $this->dir );
		$this->assertFileNotExists( $this->dir . '/foo.txt' );
		$this->assertFileNotExists( $this->dir . '/bar.txt' );
	}

	public function testRemovesDeeply() {
		mkdir( $this->dir );
		$handle = fopen( $this->dir . '/foo.txt', 'w' );
		fwrite( $handle, 'Testing Beans' );
		fclose( $handle );

		// Now create a subdirectory with a file.
		mkdir( $this->dir . '/subDir' );
		copy( $this->dir . '/foo.txt', $this->dir . '/subDir/bar.txt' );

		// Now create a sub-subdirectory with a file.
		mkdir( $this->dir . '/subDir/subSubDir' );
		$handle = fopen( $this->dir . '/subDir/subSubDir/baz.log', 'w' );
		fwrite( $handle, 'Log: Testing Beans' );
		fclose( $handle );
		copy( $this->dir . '/subDir/subSubDir/baz.log', $this->dir . '/subDir/subSubDir/foobar.log' );

		$this->directoryExists( $this->dir );
		$this->assertFileExists( $this->dir . '/foo.txt' );

		$this->directoryExists( $this->dir . '/subDir' );
		$this->assertFileExists( $this->dir . '/subDir/bar.txt' );

		$this->directoryExists( $this->dir . '/subDir/subSubDir' );
		$this->assertFileExists( $this->dir . '/subDir/subSubDir/baz.log' );
		$this->assertFileExists( $this->dir . '/subDir/subSubDir/foobar.log' );

		$this->assertTrue( beans_remove_dir( $this->dir ) );

		$this->assertDirectoryNotExists( $this->dir );
		$this->assertFileNotExists( $this->dir . '/foo.txt' );

		$this->assertDirectoryNotExists( $this->dir . '/subDir' );
		$this->assertFileNotExists( $this->dir . '/subDir/bar.txt' );

		$this->assertDirectoryNotExists( $this->dir . '/subDir/subSubDir' );
		$this->assertFileNotExists( $this->dir . '/subDir/subSubDir/baz.log' );
		$this->assertFileNotExists( $this->dir . '/subDir/subSubDir/foobar.log' );
	}
}
