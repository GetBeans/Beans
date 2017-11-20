<?php

namespace Beans\Framework\Tests\UnitTests\API\Utilities;

use Beans\Framework\Tests\UnitTests\Test_Case;

/**
 * Class Tests_Beans_Sanitize_Path
 *
 * @package Beans\Framework\Tests\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Sanitize_Path extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	/**
	 * Test beans_sanitize_path() should sanitize for filesystem.
	 */
	public function test_should_sanitize_for_filesystem() {
		$this->assertSame( BEANS_TESTS_DIR, beans_sanitize_path( BEANS_TESTS_DIR ) );
		$this->assertSame( __DIR__, beans_sanitize_path( __DIR__ ) );
		$this->assertSame( BEANS_TESTS_DIR, beans_sanitize_path( __DIR__ . '/../../../' ) );
		$this->assertSame( BEANS_TESTS_DIR . '/bootstrap.php', beans_sanitize_path( __DIR__ . '/../../../bootstrap.php' ) );
	}

	/**
	 * Test beans_sanitize_path() should remove Windows drive.
	 */
	public function test_should_remove_windows_drive() {
		$this->assertSame( '/Program Files/tmp/', beans_sanitize_path( 'C:\Program Files\tmp\\' ) );
		$this->assertSame( '/Foo/bar/baz/index.txt', beans_sanitize_path( 'D:\Foo\bar\baz\index.txt' ) );
	}
}
