<?php
/**
 * Tests for beans_scandir()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansScandir
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 */
class Tests_BeansScandir extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_scandir() should return false when directory is invalid.
	 */
	public function test_should_return_false_when_directory_is_invalid() {
		$this->assertFalse( beans_scandir( 'directory-does-not-exist' ) );
	}

	/**
	 * Test beans_scandir() should return all files in the given directory.
	 */
	public function test_should_return_all_files_in_given_directory() {
		$files = beans_scandir( __DIR__ );
		$this->assertContains( 'beansArrayUnique.php', $files );
		$this->assertContains( 'beansGet.php', $files );
		$this->assertContains( 'beansIsUri.php', $files );
		$this->assertContains( 'beansIsUrl.php', $files );
		$this->assertContains( 'beansJoinArrays.php', $files );
		$this->assertContains( 'beansScandir.php', $files );
	}

	/**
	 * Test beans_scandir() should not contain dot files.
	 */
	public function test_should_not_contain_dot_files() {
		$files = beans_scandir( __DIR__ );
		$this->assertNotContains( '.', $files );
		$this->assertNotContains( '..', $files );
	}
}
