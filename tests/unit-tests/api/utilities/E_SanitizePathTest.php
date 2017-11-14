<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Brain\Monkey\Functions;
use Beans\BeansFramework\Tests\BeansTestCase;

class F_SanitizePathTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		// Make sure the file is loaded first.
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testFilesystemPath() {
		$this->assertSame( BEANS_TESTS_DIR, beans_sanitize_path( BEANS_TESTS_DIR ) );
		$this->assertSame( dirname( __FILE__ ), beans_sanitize_path( dirname( __FILE__ ) ) );
		$this->assertSame( BEANS_TESTS_DIR, beans_sanitize_path( __DIR__ . '/../../../' ) );
		$this->assertSame( BEANS_TESTS_DIR . '/bootstrap.php', beans_sanitize_path( __DIR__ . '/../../../bootstrap.php' ) );
	}

	public function testRemoveWindowsDrive() {
		$this->assertSame( '/Program Files/tmp/', beans_sanitize_path( 'C:\Program Files\tmp\\' ) );
		$this->assertSame( '/Foo/bar/baz/index.txt', beans_sanitize_path( 'D:\Foo\bar\baz\index.txt' ) );
	}
}
