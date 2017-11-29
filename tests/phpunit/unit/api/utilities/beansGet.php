<?php
/**
 * Tests for beans_get()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansGet
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGet extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
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

		$data = array(
			'foo' => 'found me',
		);
		$this->assertEquals( 'found me', beans_get( 'foo', $data, 10 ) );
		$this->assertEquals( 'found me', beans_get( 'foo', (object) $data, 10 ) );

		$data = array(
			'baz' => 'zab',
			'rab' => 'bar',
			'red',
		);
		$this->assertEquals( 'red', beans_get( 0, $data ) );
		$this->assertEquals( 'zab', beans_get( 'baz', $data ) );
		$this->assertEquals( 'red', beans_get( 0, (object) $data ) );
		$this->assertEquals( 'zab', beans_get( 'baz', (object) $data ) );
	}
}
