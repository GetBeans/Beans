<?php
/**
 * Tests for beans_array_unique().
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansArrayUnique
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 * @group   api-uikit
 */
class Tests_BeansArrayUnique extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_array_unique() should return the original array when no duplicates are found.
	 */
	public function test_should_return_original_array_when_no_duplicates() {
		$array = [ 'foo', 5, 'bar', 47 ];
		$this->assertSame( $array, beans_array_unique( $array ) );

		$array = [ 'foo', 'bar', 'Beans', 'WordPress' ];
		$this->assertSame( $array, beans_array_unique( $array ) );
	}

	/**
	 * Test beans_array_unique() should re-index the original array.
	 */
	public function test_should_reindex_original_array() {
		$actual = [
			'foo',
			5  => 5,
			10 => 'bar',
			15 => 47,
		];
		$this->assertSame( [ 'foo', 5, 'bar', 47 ], beans_array_unique( $actual ) );

		$actual   = [
			'oof'   => 'foo',
			'rab'   => 'bar',
			'beans' => 'Beans',
			'wp'    => 'WordPress',
		];
		$expected = [ 'foo', 'bar', 'Beans', 'WordPress' ];
		$this->assertSame( $expected, beans_array_unique( $actual ) );
	}

	/**
	 * Test beans_array_unique() should remove duplicates and re-index the array.
	 */
	public function test_should_remove_duplicates_and_reindex_array() {
		$actual = [
			'foo',
			5  => 5,
			10 => 'bar',
			'foo',
			15 => 47,
			'bar',
			5,
		];
		$this->assertSame( [ 'foo', 5, 'bar', 47 ], beans_array_unique( $actual ) );

		$actual   = [
			'oof'   => 'foo',
			'rab'   => 'bar',
			'beans' => 'Beans',
			'bar',
			'foo',
			'wp'    => 'WordPress',
			'Beans',
			'foo',
			'foo'   => 'bar',
		];
		$expected = [ 'foo', 'bar', 'Beans', 'WordPress' ];
		$this->assertSame( $expected, beans_array_unique( $actual ) );
	}
}
