<?php

namespace Beans\Framework\Tests\UnitTests\API\Utilities;

use Beans\Framework\Tests\UnitTests\Test_Case;
use Brain\Monkey\Functions;
use org\bovigo\vfs\vfsStream;

/**
 * Class Tests_Beans_Get
 *
 * @package Beans\Framework\Tests\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Get extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	/**
	 * Test beans_get() returns the default value.
	 */
	public function test_should_return_default() {
		$this->assertEquals( 10, beans_get( 'foo', 'bar', 10 ) );
		$this->assertNull( beans_get( 'foo', array( 'oof' => 'found me' ) ) );
		$this->assertNull( beans_get( 'foo', array( 10, 'bar', 'baz' ) ) );
		$this->assertFalse( beans_get( 'foo', (object) array( 'bar', 'baz' ), false ) );
	}

	/**
	 * Test beans_get() should find the needle.
	 */
	public function test_should_find_needle() {
		$this->assertEquals( 'bar', beans_get( 0, 'bar', 10 ) );
		$this->assertEquals( 'found me', beans_get( 'foo', array( 'foo' => 'found me' ), 10 ) );
		$this->assertEquals( 'red', beans_get( 0, array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
		$this->assertEquals( 'zab', beans_get( 'baz', array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );

		$this->assertEquals( 'found me', beans_get( 'foo', (object) array( 'foo' => 'found me' ), 10 ) );
		$this->assertEquals( 'red', beans_get( 0, (object) array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
		$this->assertEquals( 'zab', beans_get( 'baz', (object) array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
	}
}
